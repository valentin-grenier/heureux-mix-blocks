<?php

/**
 * Plugin Name:       Heureux Mix Blocks
 * Description:       Set of custom blocks for the Heureux Mix theme.
 * Requires at least: 6.6
 * Requires PHP:      7.2
 * Version:           0.1.0
 * Author:            Valentin Grenier • Studio Val
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       heureux-mix
 *
 * @package StudioVal
 */


if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
	require __DIR__ . '/vendor/autoload.php';
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function heureux_mix_blocks_init()
{
	register_block_type(__DIR__ . '/build/faq', ['render_callback' => 'heureux_mix_blocks_render_faq']);
	register_block_type(__DIR__ . '/build/table-of-contents', ['render_callback' => 'heureux_mix_blocks_render_table_of_contents']);
	register_block_type(__DIR__ . '/build/custom-navigation', ['render_callback' => 'heureux_mix_blocks_render_custom_navigation']);
}
add_action('init', 'heureux_mix_blocks_init');

// == Server side rendering for FAQ block
function heureux_mix_blocks_render_faq($attributes)
{
	# Default query arguments
	$args = array(
		'post_type' => 'question',
		'posts_per_page' => -1,
		'post_status' => 'publish',
	);

	# Check if a taxonomy is selected in block attributes and add it to the query
	if ($attributes['taxonomy'] !== "all") {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'question-taxonomy',
				'field' => 'term_id',
				'terms' => $attributes['taxonomy'],
			),
		);
	}

	# Fetch filtered questions
	$questions = get_posts($args);

	if (count($questions) == 0) {
		return __("Désolé, il n'y a pas encore de questions à afficher.", "heureux-mix");
	}

	$context = wp_interactivity_data_wp_context(array('isOpen' => false));

	$markup = '<div class="wp-block-heureux-mix-faq">';
	foreach ($questions as $question) {
		$question_title = esc_html($question->post_title);
		$question_content = apply_filters('the_content', $question->post_content);

		$markup .= '<div data-wp-interactive="faqToggle"' . $context . ' data-wp-watch="callbacks.logIsOpen" data-wp-class--is-opened="context.isOpen" class="faq__item">';
		$markup .= '<span data-wp-on--click="actions.toggle" class="faq__question">' . $question_title . '</span>';
		$markup .= '<div class="faq__answer">' . $question_content . '</div>';
		$markup .= '</div>';
	}
	$markup .= '</div>';
	return $markup;
}

// == Server-side rendering for Quick Access block
function heureux_mix_blocks_render_table_of_contents($attributes)
{
	# Retrieve and parse blocks from post content
	$blocks = parse_blocks(get_the_content());

	# Initialize an array for storing <h2> headings
	$headings = [];

	# Loop through blocks to find <h2> headings
	foreach ($blocks as $block) {
		if ($block['blockName'] === 'core/heading' && strpos($block['innerContent'][0], '<h2') !== false) {
			$headings[] = $block['innerContent'][0];
		}
	}

	# If no <h2> headings are found, return an empty string
	if (!$headings) return;

	$table_of_contents = '<div class="wp-block-heureux-mix-table-of-contents">';
	$table_of_contents .= '<span>' . __("Plan de l'article", "heureux-mix") . '</span>';
	$table_of_contents .= '<ul>';
	foreach ($headings as $heading) {
		$heading = strip_tags($heading);
		$table_of_contents .= '<li><a href="#' . sanitize_title($heading) . '">' . $heading . '</a></li>';
	}
	$table_of_contents .= '</ul>';
	$table_of_contents .= '</div>';

	return $table_of_contents;
}

// == Server-side rendering for Custom Navigation block
function heureux_mix_blocks_render_custom_navigation($attributes)
{
	$buttonText = $attributes['buttonText'];
	$menuId = $attributes['menuId'];

	# Get the menu by its ID
	$menu = get_post($menuId);

	# Get the menu content
	$menuContent = ($menu) ? apply_filters('the_content', $menu->post_content) : 'No menu found.';

	# Build the markup
	$markup = sprintf(
		'<div class="wp-block-heureux-mix-custom-navigation" data-wp-interactive="burgerToggle" data-wp-context="{ "isVisible": false }">
            <button class="wp-block-heureux-mix-custom-navigation__button" data-wp-on--click="actions.toggle">
                %s
				<img src="%s" alt="burger icon"/>
            </button>
            <div class="wp-block-heureux-mix-custom-navigation__content" data-wp-interactive="burgerToggle" data-wp-class--is-visible="context.isVisible">
                <div class="wp-block-heureux-mix-custom-navigation__menu">
					<ul class="wp-block-navigation-menu">%s</ul>
                </div>
				<div class="wp-block-heureux-mix-custom-navigation__logo" data-wp-class--is-visible="context.isVisible"></div>
            </div>
        </div>',
		esc_html($buttonText),
		esc_url(plugins_url('src/custom-navigation/assets/burger-icon.svg', __FILE__)),
		wp_kses_post($menuContent) // Escape menu content while allowing HTML
	);

	return $markup;
}

// == On single post, get the content, parse heading and add an anchor to each heading
function heureux_mix_add_anchor_to_heading($content)
{
	if (is_single()) {
		$updated_content = '';

		# Parse the content to get all blocks
		$blocks = parse_blocks($content);

		foreach ($blocks as &$block) {
			# Check if the block is a heading and specifically an <h2>
			if ($block['blockName'] === 'core/heading' && strpos($block['innerContent'][0], '<h2') !== false) {
				# Extract the heading text to generate an ID
				preg_match('/<h2[^>]*>(.*?)<\/h2>/', $block['innerContent'][0], $matches);

				if (!empty($matches[1])) {
					$heading_text = sanitize_title($matches[1]);
					$anchor = 'id="' . $heading_text . '"';

					// Replace double hyphens with single hyphen
					$anchor = str_replace('--', '-', $anchor);

					// Add the anchor to the <h2> tag
					$block['innerContent'][0] = str_replace('<h2', '<h2 ' . $anchor, $block['innerContent'][0]);
				}
			}
		}

		# Reconstruct the content from the modified blocks
		foreach ($blocks as $block) {
			$updated_content .= render_block($block);
		}

		return $updated_content;
	}

	return $content;
}
add_filter('the_content', 'heureux_mix_add_anchor_to_heading');
