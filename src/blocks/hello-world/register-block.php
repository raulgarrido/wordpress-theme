<?php
/**
 * Register Block Type
 * all $args in https://developer.wordpress.org/reference/classes/wp_block_type/__construct/
*/

$block_name = "hello-world";
$block_dir_path = get_template_directory() . "/src/blocks/" . $block_name . "/" . $block_name . ".php";

register_block_type(
	get_template_directory() . '/build/blocks/' . $block_name,
	array(
		'render_callback' => function() use($block_dir_path) {
			return "<p>Gutenber Block, Editor and Public View.</p>";
		},
	),
);