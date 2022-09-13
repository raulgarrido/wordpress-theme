<?php
/**
 * Register Block Type
 * all $args in https://developer.wordpress.org/reference/classes/wp_block_type/__construct/
*/

$block_name = "hello-dynamic";
$block_dir_path = get_template_directory() . "/src/blocks/" . $block_name . "/" . $block_name . ".php";

register_block_type(
	get_template_directory() . '/build/blocks/' . $block_name,
	array(
		'render_callback' => function() use($block_name){
			ob_start();
			include($block_name . '.php');
			?>
			<?php
			$template_part = ob_get_contents();
			ob_end_clean();
			return $template_part;
		},
	),
);