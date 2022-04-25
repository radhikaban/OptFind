<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/19/2017
 * Time: 10:44 AM
 */


add_filter('woocommerce_product_tabs', 'km_remove_tab', 100);


function km_remove_tab($tabs)
{
    global $product;
    if (isset($tabs['pwb_tab'])) {
        $brands = wp_get_object_terms($product->get_id(), 'pwb-brand');
        if (count($brands) < 1) {
            unset($tabs['pwb_tab']);
        }

    }

    return $tabs;
}

add_action('init', 'no_mo_dashboard');
function no_mo_dashboard()
{

    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }

    if (!current_user_can('manage_options') && is_admin()) {
        $user = wp_get_current_user();
        $vendor = get_wcmp_vendor($user->ID);
        if ($vendor) {
            wp_redirect(wcmp_get_vendor_dashboard_endpoint_url('dashboard'));
            exit;
        } else {
            wp_redirect(home_url());
            exit;
        }

    }
}


add_action('user_register', 'kmsp_save_payment_method_of_user', 10, 1);

function kmsp_save_payment_method_of_user($user_id)
{
    update_user_meta($user_id, '_vendor_payment_mode', 'direct_bank');
}

add_action('init', 'km_save_payment_method_for_logged_user');
function km_save_payment_method_for_logged_user()
{
    if (is_user_logged_in()) {
        $user = wp_get_current_user();
        $vendor = get_wcmp_vendor($user->ID);
        if ($vendor) {
            kmsp_save_payment_method_of_user($user->ID);
        }
    }

}

add_filter('password_hint', 'km_change_password_hint');
function km_change_password_hint($hint)
{
    return "Hint: The password should be at least twelve characters long.";
}

add_action('wp_enqueue_scripts', 'km_include_scripts');
function km_include_scripts()
{
    $path = get_stylesheet_directory_uri();

    wp_enqueue_style('km-responsive-tabs-css', $path . "/kinex/assets/css/easy-responsive-tabs.css");
    wp_enqueue_script('km-custom-upload-js', $path . "/kinex/assets/js/custom-upload.js", array('jquery'));

    wp_enqueue_script('km-responsive-tabs-js', $path . "/kinex/assets/js/easy-responsive-tabs.js", array('jquery'));
    wp_enqueue_script('km-responsive-tabs-custom', $path . "/kinex/assets/js/easy-responsive-custom.js", array('km-responsive-tabs-js'));
}


add_filter('woocommerce_process_registration_errors', 'km_register_form_validation', 10, 4);

function km_register_form_validation($validation_error, $username, $password, $email)
{

    $in_valid = false;
    $fields = array(
        'vendor_first_name',
        'vendor_last_name',
        //'vendor_website',
        'vendor_phone',
        'vendor_address_1',
        'vendor_state',
        'vendor_city',
        'vendor_postcode',
    );

    foreach ($fields as $field) {
        if (isset($_REQUEST['userdata'][$field]) && empty($_REQUEST['userdata'][$field])) {
            $in_valid = false;
            break;
        }
    }

    if ($in_valid) {
        return new WP_Error('registration-requies-fileds', __('Please fill all required fields.', 'woocommerce'));
    }

    if ((isset($_REQUEST['password']) && empty($_REQUEST['password'])) || (isset($_REQUEST['confirm_password']) && empty($_REQUEST['confirm_password']))) {
        return new WP_Error('registration-password-fileds', __('password fields are required', 'woocommerce'));
    }
    if (isset($_REQUEST['userdata'])):
        if ($_REQUEST['password'] != $_REQUEST['confirm_password']) {
            return new WP_Error('registration-requies-fileds', __('both password must be match.', 'woocommerce'));
        }
    endif;


    /*if (isset($_REQUEST['userdata']['vendor_website'])):
        $website = ($_REQUEST['userdata']['vendor_website']);
        if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $website)) {

            return new WP_Error('registration-website-fileds', __('Please enter valid website address.', 'woocommerce'));
        }
    endif;*/

    if (isset($_REQUEST['userdata']['vendor_phone'])):
        $phone = ($_REQUEST['userdata']['vendor_phone']);
        if (!preg_match("/(\(\d{3}+\)+ \d{3}+\-\d{4}+)/", $phone)) {
            return new WP_Error('registration-telephone-fileds', __('Please enter valid telephone number.', 'woocommerce'));
        }
    endif;
    return $validation_error;

}

add_action('woocommerce_created_customer', 'km_save_register_fields', 11, 3);
function km_save_register_fields($customer_id)
{

    if (isset($_REQUEST['userdata'])) {
        foreach ($_REQUEST['userdata'] as $key => $val) {
            if ($key == 'vendor_first_name' || $key == 'vendor_last_name') {
                continue;
            }


            update_user_meta($customer_id, "_" . trim($key), trim($val));
        }
    }
    if ($_REQUEST['userdata']['vendor_first_name'] || $_REQUEST['userdata']['vendor_last_name']) {

        $v[] = $_REQUEST['userdata']['vendor_first_name'];
        $v[] = $_REQUEST['userdata']['vendor_last_name'];

        update_user_meta($customer_id, '_vendor_shop_owner', implode(" ", $v));
    }

    $street_address_fields = wc_clean($_REQUEST['userdata']['vendor_address_1']);
    $city_fields = wc_clean($_REQUEST['userdata']['vendor_city']);
    $state_fields = wc_clean($_REQUEST['userdata']['vendor_state']);
    $country_fields = wc_clean($_REQUEST['userdata']['vendor_country']);
    $postal_fields = wc_clean($_REQUEST['userdata']['vendor_postcode']);

    $address = '';

    if (!empty($street_address_fields)) {
        $address .= $street_address_fields . ", ";
    }
    if (!empty($city_fields)) {
        $address .= $city_fields . ", ";
    }
    if (!empty($state_fields)) {
        $address .= $state_fields . ", ";
    }
    if (!empty($country_fields)) {
        $address .= $country_fields . ", ";
    }
    if (!empty($postal_fields)) {
        if (!empty($address)) {
            $address = trim($address, ", ");
        }

        $address .= $postal_fields . ", ";
    }

    $geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false');
    $geo = json_decode($geo, true); // Convert the JSON to an array

    if (isset($geo['status']) && ($geo['status'] == 'OK')) {
        $latitude = $geo['results'][0]['geometry']['location']['lat']; // Latitude
        $longitude = $geo['results'][0]['geometry']['location']['lng']; // Longitude

        insert_vendor_lat($customer_id,$latitude,$longitude);

    }

}


add_action('storefront_before_content', 'km_before_header');

function km_before_header()
{
    get_template_part('kinex/templates/page-header');
}

add_action('wp_ajax_km_shop_product_filter', 'km_shop_product_filter');
add_action('wp_ajax_nopriv_km_shop_product_filter', 'km_shop_product_filter');

function km_shop_product_filter()
{

    global $WCMp, $wpdb;

    $dc_vendor_id = get_user_meta(get_current_user_id(), '_vendor_term_id', true);

    if (!isset($_POST['_nonce']) || !wp_verify_nonce($_POST['_nonce'], 'km_shop_product_filter')) {
        print 'Sorry, your nonce did not verify.';
        exit;
    } else {


        $tax_query[] =
            array(
                'taxonomy' => 'dc_vendor_shop',
                'field' => 'id',
                'terms' => get_user_meta($_POST['vendor_id'], '_vendor_term_id', true)
            );

        if (isset($_POST['product_cat']) && !empty($_POST['product_cat'])) {


            if (isset($_POST['product_cat']) && $_POST['product_cat'] == "-1") {

            } else {

                $tax_query[] = array(
                    'taxonomy' => 'product_cat',
                    'field' => 'slug',
                    'terms' => $_POST['product_cat'],
                    'operator' => 'IN'
                );

            }

        }
        if (isset($_POST['shop_s'])) {
            $args['s'] = $_POST['shop_s'];
            $args['suppress_filters'] = false;
        }
        $args['tax_query'] = $tax_query;
        $args['post_type'] = 'product';
        if (isset($_POST['pagenumber'])) {
            $args['paged'] = $_POST['pagenumber'];
        } else {
            $args['paged'] = 1;
        }
        if (isset($_POST['postperpage'])) {
            $args['posts_per_page'] = $_POST['postperpage'];
            //  $args['paged'] =1;
        } else {
            $args['posts_per_page'] = KM_POST_PER_PAGE;
        }

        if (!empty($_POST['orderby'])) {
            $orderby=$order='';
            $orderby_value = isset( $_POST['orderby'] ) ? wc_clean( (string) $_POST['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

            // Get order + orderby args from string
            $orderby_value = explode( '-', $orderby_value );
            $orderby       = esc_attr( $orderby_value[0] );
            $order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;


            $ordering_args = WC()->query->get_catalog_ordering_args($orderby, $order);

            $args['meta_key'] = $ordering_args['meta_key'];
            $args['orderby'] = $ordering_args['orderby'];
            $args['order'] = $ordering_args['order'];
        }

        if (isset($_REQUEST['s']) && empty($_REQUEST['s'])) {
            unset($_REQUEST['s']);
        }


        if (isset($_REQUEST['load_more']) && !empty($_REQUEST['load_more'])) {
            $WCMp->template->get_template('single-product.php', array('args' => $args));
        } else {
            echo wc_get_template('shop-archive-products.php', array('args' => $args, 's_vendor_id' => $_REQUEST['vendor_id']));
            //$WCMp->template->get_template('archive-products.php', array('args' => $args));
        }
        die;
    }
}

//add_action('get_header', 'remove_storefront_sidebar');
function remove_storefront_sidebar()
{
    if (is_woocommerce()) {
        remove_action('storefront_sidebar', 'storefront_get_sidebar', 10);
        remove_action('woocommerce_after_main_content', 'storefront_after_content', 10);
        add_action('woocommerce_after_main_content', 'storefront_after_content_wc', 10);
        add_action('woocommerce_after_main_content', 'storefront_after_content', 10);
        add_action('woocommerce_sidebar', 'storefront_get_sidebar', 10);
    }
}

function storefront_after_content_wc()
{
    ?>
    </div>
    <div class="right-part">
        <?php

        do_action('woocommerce_sidebar');
        ?>
    </div>
    </main>

    <?php
}