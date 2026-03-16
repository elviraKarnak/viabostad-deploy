<?php 
// style sheet & scripts

add_action('after_setup_theme', function() {

    if ( function_exists('get_field') ) {

        $api_key = get_field('google_map_api_key_viabostad', 'option');

        if ( $api_key && !defined('GOOGLE_MAP_API_KEY') ) {
            define('GOOGLE_MAP_API_KEY', $api_key);
        }

    }

});



function viabosted_enqueue(){

	$uri = get_theme_file_uri();
    $ver = 1.0;
    $vert = time();

      wp_register_style( 'bootstrap',   $uri. '/assets/css/bootstrap/bootstrap.min.css', [], $ver);
	  wp_register_style( 'font-awesome', $uri.'/assets/css/fontawesome/css/all.min.css', [], $ver);
	  wp_register_style( 'owl', $uri. '/assets/css/owl/owl.carousel.min.css', [], $ver);
	   wp_register_style( 'fancybox-css', 'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css', [], $ver);
	  wp_register_style( 'theme-css',  $uri. '/assets/css/main-style.css', [], $vert);
	  wp_register_style( 'theme_stylesheet', $uri. '/style.css', [], $vert);


	  wp_enqueue_style( 'bootstrap');
	  wp_enqueue_style( 'font-awesome');
	  wp_enqueue_style( 'owl');
	  wp_enqueue_style( 'fancybox-css');
	  wp_enqueue_style( 'theme-css');
	  wp_enqueue_style( 'theme_stylesheet');

	  wp_register_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . GOOGLE_MAP_API_KEY.'&libraries=places', [], $ver, true );
	  wp_register_script( 'bootstrap', $uri . '/assets/js/bootstrap/bootstrap.bundle.min.js', [], $ver, true );
	  wp_register_script( 'owl',     $uri . '/assets/js/owl/owl.carousel.min.js',  [], $ver, true );
	  wp_register_script( 'fancybox-js',     'https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js',  [], $ver, true );
	  wp_register_script( 'custom-js', $uri . '/assets/js/function.js', [], $vert, true );
	  wp_register_script( 'ajax-functions', $uri . '/assets/js/function-ajax.js', [], $vert, true );



	  wp_enqueue_script('jquery');
	  wp_enqueue_script('google-maps');
	  wp_enqueue_script('bootstrap');
	  wp_enqueue_script('owl');
	  wp_enqueue_script('fancybox-js');
	  wp_enqueue_script('custom-js');
	  wp_enqueue_script('ajax-functions');

	  wp_localize_script(
        'ajax-functions',
        'viabostad_functions', // JS object name
        array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('viabostad_nonce'),
			'login'    => home_url('/members'),
			'is_user_logged_in' => is_user_logged_in(),
        )
    );

  }

  add_action( 'wp_enqueue_scripts', 'viabosted_enqueue' );



// register navs
register_nav_menus(
	array(
		'menu-1' => __('Primary', 'viabosted'),
		'menu-3' => __('Login', 'viabosted'),
		'menu-2' => __('Footer First Menu', 'viabosted'),
    )
);

	// theme support
if ( ! function_exists( 'viabosted_setup_theme' ) ) {

	function viabosted_setup_theme() {

		// Basic supports
		add_theme_support( 'custom-logo' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'align-wide' );

		// WooCommerce support
		add_theme_support( 'woocommerce' );

		// Optional (recommended)
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}

}
add_action( 'after_setup_theme', 'viabosted_setup_theme', 20 );


require get_template_directory() . '/inc/custom_functions.php';
require get_template_directory() . '/inc/acf-blocks-support.php';
require get_template_directory() . '/inc/property-function.php';
require get_template_directory() . '/inc/bp-notifications.php';

// shortcodes
require get_template_directory() . '/inc/shortcodes/property-listing.php';
require get_template_directory() . '/inc/shortcodes/home-search.php';



add_filter('yith_wcwl_supported_post_types', function($post_types){
    $post_types[] = 'property'; // your CPT slug
    return $post_types;
});


function my_acf_google_map_api( $api ) {
    $api['key'] = GOOGLE_MAP_API_KEY;
    return $api;
}

add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');


function reading_time($content) {
    $word_count = str_word_count( wp_strip_all_tags($content) );
    return ceil($word_count / 200);
}


// add_action('template_redirect', function () {

//     // If user already logged in → do nothing
//     if (is_user_logged_in()) {
//         return;
//     }

//     // Check BuddyPress member pages
//     if (function_exists('bp_is_user') && bp_is_user()) {

//         // Redirect to WooCommerce My Account page
//         wp_redirect(wc_get_page_permalink('myaccount'));
//         exit;
//     }

// });

function create_property_admin_role() {

    add_role(
        'property_admin',
        'Property Admin',
        array(

            'read' => true,

            // Allow editing ALL post types
            'edit_posts' => true,
            'edit_others_posts' => true,
            'edit_published_posts' => true,
            'edit_private_posts' => true,
            'publish_posts' => true,
            'delete_posts' => true,
            'delete_others_posts' => true,

            // Pages
            'edit_pages' => true,
            'edit_others_pages' => true,
            'edit_published_pages' => true,
            'edit_private_pages' => true,
            'publish_pages' => true,
            'delete_pages' => true,
            'delete_others_pages' => true,

            // Custom Post Types (generic caps used by many CPTs)
            'edit_others_posts' => true,
            'delete_others_posts' => true,
            'publish_posts' => true,
            'read_private_posts' => true,

            // Media
            'upload_files' => true,

            // Comments
            'moderate_comments' => true,

            // Block plugins
            'install_plugins' => false,
            'activate_plugins' => false,
            'update_plugins' => false,
            'delete_plugins' => false,
            'edit_plugins' => false,

            // Block themes
            'switch_themes' => false,
            'edit_themes' => false,
            'install_themes' => false,
            'update_themes' => false,

            // Block file editor
            'edit_files' => false
        )
    );

}
add_action('init', 'create_property_admin_role');

function restrict_editor_delete_permissions() {

    $role = get_role('editor');

    if ($role) {

        // Prevent deleting others' posts
        $role->remove_cap('delete_others_posts');
        $role->remove_cap('delete_others_pages');

        // Allow deleting their own
        $role->add_cap('delete_posts');
        $role->add_cap('delete_pages');
    }

}
add_action('init', 'restrict_editor_delete_permissions');

function give_editor_menu_customizer_access() {

    $role = get_role('editor');

    if ($role) {
        $role->add_cap('edit_theme_options');
    }

}
add_action('init', 'give_editor_menu_customizer_access');