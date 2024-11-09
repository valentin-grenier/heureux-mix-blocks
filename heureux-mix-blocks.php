<?php

/**
 * Plugin Name:       Heureux Mix Blocks
 * Description:       Set of custom blocks for the Heureux Mix theme.
 * Requires at least: 6.6
 * Requires PHP:      7.2
 * Version:           0.1.0
 * Author:            The WordPress Contributors
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
	register_block_type(__DIR__ . '/build/quick-access', ['render_callback' => 'heureux_mix_blocks_render_quick_access']);
}
add_action('init', 'heureux_mix_blocks_init');

// == Server side rendering for FAQ block
function heureux_mix_blocks_render_faq($attributes)
{
	$args = array(
		'post_type' => 'question',
		'posts_per_page' => 5
	);

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
		//$markup .= '<div class="faq__answer" data-wp-bind--hidden="!context.isOpen">' . $question_content . '</div>';
		$markup .= '<div class="faq__answer">' . $question_content . '</div>';
		$markup .= '</div>';
	}
	$markup .= '</div>';
	return $markup;
}

// == Server-side rendering for Quick Access block
function heureux_mix_blocks_render_quick_access($attributes)
{
	// Retrieve and parse blocks from post content
	$blocks = parse_blocks(get_the_content());

	// Initialize an array for storing <h2> headings
	$headings = [];


	// Loop through blocks to find <h2> headings
	foreach ($blocks as $block) {
		if ($block['blockName'] === 'core/heading' && $block['attrs']['level'] === 2) {
			$headings[] = $block['innerHTML'];
		} else {
			$heading[] = 'No heading found';
		}
	}

	return $heading[0];
}
