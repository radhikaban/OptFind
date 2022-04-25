<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/18/2017
 * Time: 2:31 PM
 */

/***
 * Start Product Favourite Button Hooks
 */


add_action('wp_enqueue_scripts', 'enqueue_scrpits');
function enqueue_scrpits()
{

    $script_path = get_stylesheet_directory_uri() . "/kinex/assets/js/";
    wp_register_script('km_favourites_scripts', $script_path . 'add-to-favourites.js', array('jquery'), '1.0.0');
    wp_register_script('km_validate_form', $script_path . 'jquery.validate.js', array('jquery'), '1.0.0');
    wp_register_script('km_input_mask', $script_path . 'jquery.maskedinput.min.js', array('jquery'), '1.0.0');
    wp_enqueue_script('km_favourites_scripts');
    wp_enqueue_script('km_validate_form');
    wp_enqueue_script('km_input_mask');

    wp_localize_script("km_favourites_scripts", 'fajax', array('ajaxurl' => admin_url('admin-ajax.php')));
    $simple_nonce = array(
        'km_favourites_product_nonce' => wp_create_nonce('km_favourites_product_nonce')
    );
    wp_localize_script('km_favourites_scripts', 'km_favourites_nonce', $simple_nonce);
}

add_action('km_get_total_order_shop', 'km_toal_order_count_by_shop');
function km_toal_order_count_by_shop($vendor_id)
{
    global $wpdb;
    $vendor = get_wcmp_vendor($vendor_id);
    $count = $wpdb->get_row("SELECT DISTINCT count(order_id) as c from `{$wpdb->prefix}wcmp_vendor_orders` where commission_id > 0 AND vendor_id = '" . $vendor->id . "'  and `is_trashed` != 1 ORDER BY `created` DESC")->c;

    return printf("%s Sales",$count);
}


function km_add_product_favourites_button()
{
    global $product;

    //    if (is_user_logged_in() && !is_user_wcmp_vendor(get_current_user_id()) ) {

    if (is_user_logged_in()) {

        $user_id = get_current_user_id();
        $favourites = KMFavourites_Product::get_favourites($user_id);

        if (in_array($product->get_id(), $favourites)) {
            include('templates/remove-to-favourites-button.php');
        } else {
            include('templates/add-to-favourites-button.php');
        }
    }
}


add_action('woocommerce_after_add_to_cart_button', 'km_add_product_favourites_button', 10);
//add_action('km_woocommerce_after_shop_loop_item', 'km_add_product_favourites_button', 10);
add_action('woocommerce_after_shop_loop_item', 'km_add_product_favourites_button', 10);

//add_action('woocommerce_after_single_product', 'km_add_product_favourites_button');

function km_show_product_favourites($atts)
{
    $GLOBALS['km_favourites_product'] = true;
    extract(shortcode_atts(array(
        'user_id' => false
    ), $atts));
    $favourites = KMFavourites_Product::get_favourites($user_id);
    require_once('templates/favourites-template.php');
    unset($GLOBALS['km_favourites_product']);
}


add_shortcode('km_product_favourite', 'km_show_product_favourites');


function km_product_favourites_button()
{
    global $product;
    if (!$product) {
        return;
    }
    if (is_user_logged_in() && !$GLOBALS['km_favourites_product']) {
        include('templates/add-to-favourites-button.php');
    }
}


add_shortcode('km_product_favourites_button', 'km_product_favourites_button');


add_action('km_get_count_shop_favourites', 'km_get_count_shop_favourites_function');
function km_get_count_shop_favourites_function($vendor_id)
{
    include('templates/get-count-favourites-button.php');
}

//ADD TO FAVOURITES
add_action("wp_ajax_km_product_add_to_favourites", "km_product_add_to_favourites");
add_action("wp_ajax_nopriv_km_product_add_to_favourites", "km_product_add_to_favourites");

function km_product_add_to_favourites()
{

    $message = array();
    $result = check_ajax_referer('km_favourites_product_nonce', 'km_favourites_product_nonce', false);
    if (false !== $result) {
        $favourites = KMFavourites_Product::get_favourites();

        $prod_id = sanitize_text_field($_POST['prod_id']);
        if (!in_array($prod_id, $favourites)) {
            $prod_id = (int)$prod_id;
            array_push($favourites, $prod_id);
            KMFavourites_Product::update_favourites($favourites);

            $message['response'] = 'success';
            $message['msg'] = 'This product has been added to your favourites products.';
            $message['btn'] = '<button class="simple-remove-from-favourites" data-productid="' . $prod_id . '">Remove Favourite</button>';
            echo json_encode(($message));
            die();
        }
        $message['response'] = 'success';
        $message['btn'] = '<button class="simple-remove-from-favourites" data-productid="' . $prod_id . '">Remove Favourite</button>';
        echo json_encode(($message));
        die();
    }
    $message['response'] = 'error';
    $message['msg'] = 'Something went wrong';
    echo json_encode(($message));
    die();
}

//REMOVE FROM FAVOURITES
add_action("wp_ajax_km_ajax_remove_from_favourites", "km_ajax_remove_from_favourites");
add_action("wp_ajax_nopriv_km_ajax_remove_from_favourites", "km_ajax_remove_from_favourites");

function km_ajax_remove_from_favourites()
{
    $result = check_ajax_referer('km_favourites_product_nonce', 'km_favourites_product_nonce', false);
    if (false !== $result) {

        $prod_id = (int)sanitize_text_field($_POST['prod_id']);
        $user_id = get_current_user_id();
        KMFavourites_Product::remove($user_id, $prod_id);
        $message['response'] = 'success';
        $message['msg'] = 'This has been remove to your favourites products.';
        $message['btn'] = '<button class="simple_add_to_favourites" data-productid="' . $prod_id . '">Add to Favorites</button>';
        echo json_encode(($message));
        die();


    }
    $message['response'] = 'error';
    $message['msg'] = 'Something went wrong';
    echo json_encode(($message));
    die();
}


/***
 * End Product Favourite Button Hooks
 */


/***
 * Start Start Favourite Button Hooks
 */


function km_add_shop_favourites_button($shop_id)
{
    if (is_user_logged_in()) {
//    if (is_user_logged_in() && !is_user_wcmp_vendor(get_current_user_id()) ) {
        $user_id = get_current_user_id();
        $favourites = KMFavourites_Shop::get_favourites($user_id);

        if (in_array($shop_id, $favourites)) {
            require_once('templates/remove-to-shop-favourites-button.php');
        } else {
            require_once('templates/add-to-shop-favourites-button.php');
        }

    }
}


add_action('km_get_favourite_shop_button', 'km_add_shop_favourites_button', 10);
add_shortcode('km_shop_favourites_button', 'km_add_shop_favourites_button');


function km_show_shop_favourites($atts)
{
    $GLOBALS['km_favourites_shop'] = true;
    extract(shortcode_atts(array(
        'user_id' => false
    ), $atts));
    $favourites = KMFavourites_Shop::get_favourites($user_id);
    require_once('templates/favourites-shop-template.php');
    unset($GLOBALS['km_favourites_shop']);
}

add_shortcode('show_shop_favourites', 'km_show_shop_favourites');


//ADD TO FAVOURITES
add_action("wp_ajax_km_shop_add_to_favourites", "km_shop_add_to_favourites");
add_action("wp_ajax_nopriv_km_shop_add_to_favourites", "km_shop_add_to_favourites");

function km_shop_add_to_favourites()
{
    $message = array();
    $result = check_ajax_referer('km_favourites_product_nonce', 'km_favourites_product_nonce', false);
    if (false !== $result) {
        $favourites = KMFavourites_Shop::get_favourites();

        $prod_id = sanitize_text_field($_POST['prod_id']);
        if (!in_array($prod_id, $favourites)) {
            $prod_id = (int)$prod_id;
            array_push($favourites, $prod_id);
            KMFavourites_Shop::update_favourites($favourites);

            $message['response'] = 'success';
            $message['msg'] = 'This shop has been added to your favourites shop.';
            $message['btn'] = '<button class="simple-remove-from-shop-favourites" data-shopid="' . $prod_id . '">Remove Favourite Shop</button>';
            echo json_encode(($message));
            die();
        }
        $message['response'] = 'success';
        $message['msg'] = 'This shop is already in your favourites shop.';
        $message['btn'] = '<button class="simple-remove-from-shop-favourites" data-shopid="' . $prod_id . '">Remove Favourite Shop</button>';
        echo json_encode(($message));
        die();
    }
    $message['response'] = 'error';
    $message['msg'] = 'Something went wrong';
    echo json_encode(($message));
    die();
}

//REMOVE FROM FAVOURITES
add_action("wp_ajax_km_ajax_remove_from_shop_favourites", "km_ajax_remove_from_shop_favourites");
add_action("wp_ajax_nopriv_km_ajax_remove_from_shop_favourites", "km_ajax_remove_from_shop_favourites");

function km_ajax_remove_from_shop_favourites()
{
    $result = check_ajax_referer('km_favourites_product_nonce', 'km_favourites_product_nonce', false);
    if (false !== $result) {

        $prod_id = (int)sanitize_text_field($_POST['prod_id']);
        $user_id = get_current_user_id();
        KMFavourites_Shop::remove($user_id, $prod_id);
        $message['response'] = 'success';
        $message['msg'] = 'This has been remove to your favourites shop.';
        $message['btn'] = '<button class="simple_add_to_shop_favourites" data-shopid="' . $prod_id . '">Add Favorites Shop</button>';
        echo json_encode(($message));
        die();


    }
    $message['response'] = 'error';
    $message['msg'] = 'Something went wrong';
    echo json_encode(($message));
    die();
}
