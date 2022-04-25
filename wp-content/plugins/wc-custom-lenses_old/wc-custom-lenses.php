<?php
/*
Plugin Name: Woocommerce Custom Lenses
Plugin URI: http://kinexmedia.com/
Description: This plugin allow WooCommerce store admin to add custom lenses for glasess and add to cart as addons products.
Version: 1.0
Author: Kinexmedia
Text Domain: wc-custom-lenses
*/

if (
in_array(
    'woocommerce/woocommerce.php',
    apply_filters('active_plugins', get_option('active_plugins'))
)
) {

    // Define WC_PLUGIN_FILE.
    if ( ! defined( 'KMCL_PLUGIN_FILE' ) ) {
        define( 'KMCL_PLUGIN_FILE', __FILE__ );
    }

    if ( ! defined( 'KMCL_PLUGIN_URL' ) ) {
        define( 'KMCL_PLUGIN_URL', plugin_dir_url(__FILE__));
    }

    /* ======= the plugin main class =========== */
    $_plugin = dirname(__FILE__) . '/classes/plugin.class.php';
    if (file_exists($_plugin))
        include_once($_plugin);
    else
        die('Class not found!' . $_plugin);


    $f_files = dirname(__FILE__) . '/kmcl.functions.php';
    if (file_exists($f_files))
        include_once($f_files);
    else
        die('File not found! ' . $f_files);


    if (!function_exists('KMCL')) {

        function KMCL()
        {

            return KM_CustomLenses::get_instance();
        }
    }

    add_action('woocommerce_init', 'kmcl_init');
    function kmcl_init()
    {
        $kmcustomlenses = KMCL();
    }

    function kmcl_rewrite_flush() {
        kmcl_init();
        // You should *NEVER EVER* do this on every page load!!
        flush_rewrite_rules();
    }
    register_activation_hook( __FILE__, 'kmcl_rewrite_flush' );
}

//if (is_admin()) {
//
//    $_admin = dirname(__FILE__) . '/classes/admin.class.php';
//    if (file_exists($_admin))
//        include_once($_admin);
//    else
//        die('file not found! ' . $_admin);
//
//    $nmpersonalizedproduct_admin = new NM_PersonalizedProduct_Admin();
//}


/*
 * activation/install the plugin data
*/
