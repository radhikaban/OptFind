<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Express Checkout via PayPal for WooCommerce
 * Plugin URI:        https://wordpress.org/plugins/express-checkout/
 * Description:       Add a PayPal Express Checkout to your WooCommerce Website and start selling today. Developed by Official PayPal Partner.
 * Version:           4.0.2
 * Author:            wpgateways
 * Author URI:        https://profiles.wordpress.org/wpgateways/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       express-checkout
 * Domain Path:       /languages
 * Requires at least: 5.3.0
 * Tested up to: 5.6.1
 * WC requires at least: 3.0.0
 * WC tested up to: 5.0.0
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
/**
 * define plugin basename
 */
if (!defined('EXPRESS_CHECKOUT_PLUGIN_BASENAME')) {
    define('EXPRESS_CHECKOUT_PLUGIN_BASENAME', plugin_basename(__FILE__));
}


if (!defined('PEC_PLUGIN_IMAGE_URL')) {
    define('PEC_PLUGIN_IMAGE_URL', plugin_dir_url(__FILE__));
}

if (!defined('EXPRESS_CHECKOUT_PLUGIN_DIR')) {
    define('EXPRESS_CHECKOUT_PLUGIN_DIR', dirname(__FILE__));
}

if (!defined('EXPRESS_CHECKOUT_WORDPRESS_LOG_DIR')) {
    $upload_dir = wp_upload_dir();
    define('EXPRESS_CHECKOUT_WORDPRESS_LOG_DIR', $upload_dir['basedir'] . '/pal-for-edd-logs/');
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-express-checkout-activator.php
 */
function activate_express_checkout() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-express-checkout-activator.php';
    Express_Checkout_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-express-checkout-deactivator.php
 */
function deactivate_express_checkout() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-express-checkout-deactivator.php';
    Express_Checkout_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_express_checkout');
register_deactivation_hook(__FILE__, 'deactivate_express_checkout');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-express-checkout.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_express_checkout() {

    $plugin = new Express_Checkout();
    $plugin->run();
}

function init_express_checkout_gateway_class() {
    run_express_checkout();
}

add_action('plugins_loaded', 'init_express_checkout_gateway_class');
