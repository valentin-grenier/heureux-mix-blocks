# Heureux Mix FAQ Block Plugin

A custom Gutenberg block for displaying a list of FAQ items using posts of a custom post type `question`.

## Description

The **Heureux Mix FAQ Block Plugin** allows users to display FAQs dynamically on their WordPress site. The block retrieves up to 5 posts from the `question` custom post type and renders them as an FAQ list.

## Features

- Custom Gutenberg block for FAQs.
- Retrieves the latest 5 posts from the `question` post type.
- Displays each post's title as the question and content as the answer.

## Installation

1. **Upload the plugin files** to the `/wp-content/plugins/heureux-mix-faq-block` directory, or install the plugin through the WordPress plugins screen directly.
2. **Activate the plugin** through the 'Plugins' screen in WordPress.
3. Add the block to your post or page through the Gutenberg editor.

## Usage

- Ensure that you have a custom post type named `question` with content populated.
- Add the "Heureux Mix FAQ" block to your content using the Gutenberg editor.
- The block will display up to 5 FAQs based on the latest posts in the `question` post type.
