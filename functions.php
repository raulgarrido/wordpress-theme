<?php
/**
 * Theme Name:       Boilerplate
 * Description:      Blank Wordpress Theme with ACF Block, Gutenberg Blocks, and fullsite editing support.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           0.1.0
 * Author:            Raúl Garrido Vargas
 * Text Domain:       boilerplate
 *
*/
// scripts and styles managing versions
$this_theme = wp_get_theme();
define('THEME_NAME', $this_theme->get('Name'));
define('THEME_IMG_PATH', (get_template_directory_uri() . '/src/images/'));
define('THEME_TEXTDOMAIN', $this_theme->get('TextDomain'));
define('DEV_VERSION', '0.1.0.0');
// add_theme_support('woocommerce');

//Image sizes
if (function_exists('add_image_size')) {
    // add_image_size( 'max_size', 1920, 1080 );
    // add_image_size( 'xxl_size', 1536, 1024 );
    // add_image_size( 'xl_size', 1440, 864 );
    // add_image_size( 'laptop_size', 1366, 768 );
    // add_image_size( 'laptop_size_2x', 2732, 2668 );
    // add_image_size( 'tablet_size', 1280, 720 );
    // add_image_size( 'tablet_size_2x', 2560, 1440 );
    // add_image_size( 'tablet_small_size', 1100, 734 );
}

// Options Page
if (function_exists('acf_add_options_page')) {
    // menu option
    acf_add_options_page([
        'page_title'  =>  __('Theme settings'),
        'menu_title'  =>  __('Administrar Información'),
        'menu_slug' =>  'general-theme-settings',
        'capability'  =>  'edit_posts',
        'icon_url'  =>  'dashicons-admin-settings',
        'redirect'  =>  true
    ]);
}

// remove admin login header
add_action('get_header', 'remove_admin_login_header');
function remove_admin_login_header() {
	remove_action('wp_head', '_admin_bar_bump_cb');
}

class CurrentTheme {
	function __construct(){
		// Enable Block Editor for Products
		// add_filter( 'woocommerce_taxonomy_args_product_cat', array($this, 'enableWoocommerce') );
		// add_filter( 'woocommerce_taxonomy_args_product_tag', array($this, 'enableWoocommerce') );
		// add_filter( 'use_block_editor_for_post_type', array($this, 'woocommerceBlockEditor'), 10, 2 );

		// remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );
		// // add_action( 'woocommerce_review_order_after_order_total', 'woocommerce_checkout_coupon_form', 10 );
		// remove_action( 'woocommerce_checkout_order_review', 'woocommerce_order_review', 10 );
		// add_action( 'woocommerce_checkout_before_order_review', 'woocommerce_order_review', 10 );

		add_action('init', array($this, 'initConfig'));
		add_theme_support('html5', array('search-form'));
		add_theme_support('post-thumbnails');
		// remove actions unneeded
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');
		// nav menus
		add_theme_support('nav-menus');
		register_nav_menus(
			[
				'navigation_top' => 'Top navigation',
				'footer_navigation' => 'Social Media',
			]
		);
		add_action( 'woocommerce_checkout_terms_and_conditions', array($this, 'termsPage'));
		add_filter( 'query_vars', array($this, 'customQueryVars') );
	}
	function customQueryVars( $q_vars ) {
		$q_vars[] = 'paged';
		$q_vars[] = 'page';
		$q_vars[] = 'ubicacion';
		$q_vars[] = 'max-results';
		return $q_vars;
	}	
	function termsPage(){
		remove_action( 'woocommerce_checkout_terms_and_conditions', 'wc_checkout_privacy_policy_text', 20 );
		?>
		<div class="col-xxs-12 flex-center text-center padding-bottom">
			<p class="margin-zero terms-text padding">
				Tu información personal será usada para procesar tu petición y para otros propósitos descritos en las <a href="<?php echo get_privacy_policy_url(); ?>">Políticas de privacidad</a>.		
			</p>
		</div>
		<?php
	}
	function initConfig(){
		// Cute titles
		add_filter('wp_title', 'wp_strip_all_tags', 8);
		add_filter('pre_get_document_title', 'wp_strip_all_tags', 9);
		// Enable excerpts for pages
		add_post_type_support('page', 'excerpt');
		// custom post_types
		$this->customPostTypes();
	}
	function customPostTypes() {
        // Include Custom Gutenberg Blocks
        $custom_post_types_path = get_template_directory() . "/src/custom-post-types/";
        foreach (glob($custom_post_types_path . "*.php") as $this_post_type) {
			include_once($this_post_type);
        }		
	}
	function woocommerceBlockEditor($is_enabled, $post_type){
		if ( $post_type == 'product' && current_user_can( 'edit_posts' ) ) {
			$is_enabled = true;
		}
		return $is_enabled;
	}
	function enableWoocommerce($args) {
		$args['show_in_rest'] = true;
		return $args;
	}
}
$CurrentTheme = new CurrentTheme;

class CurrentBlocks {
	public function __construct(){
        add_action('acf/init', array($this, 'registerACFBlocks'));
		add_action( 'init', array($this, 'registerBlocks') );
		add_action( 'enqueue_block_editor_assets', array($this, 'adminStyles') );
		add_action( 'wp_enqueue_scripts', array($this, 'globalStyles') );
	}
    function registerBlocks(){
        // Include Custom Gutenberg Blocks
        $custom_gutenberg_blocks = get_template_directory() . "/src/blocks/";
        foreach (glob($custom_gutenberg_blocks . "*/register-block.php") as $gutenbergBlock) {
			include_once($gutenbergBlock);
        }
    }
	function registerACFBlocks(){
        // Include Custom ACF Blocks
		$custom_acf_blocks = get_template_directory() . "/src/acf-blocks/";
		foreach (glob($custom_acf_blocks . "*/index.php") as $acfBlock) {
			include_once($acfBlock);
		}   
	}
	public function globalStyles(){
		wp_enqueue_style(
			'grid-styles',
			get_template_directory_uri() . '/build/all.min.css',
			[],
			DEV_VERSION
		);

		wp_enqueue_style(
			'public-block-styles',
			get_template_directory_uri() . '/build/public-block-styles.min.css',
			[],
			DEV_VERSION
		);

		wp_enqueue_script(
			'theme-scripts',
			get_template_directory_uri() . '/src/js/theme.js',
			[],
			DEV_VERSION
		);
	}
	public function adminStyles(){
		wp_enqueue_style(
			'grid-styles',
			get_template_directory_uri() . '/build/all.min.css',
			[],
			DEV_VERSION
		);		
		wp_enqueue_style(
			'admin-block-styles',
			get_template_directory_uri() . '/build/admin-block-styles.min.css',
			[],
			DEV_VERSION
		);		
	}
}
$CurrentBlocks = new CurrentBlocks;
