<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/6/2017
 * Time: 12:54 PM
 */


/**
 *  Add Product url;
 */


add_filter('wcmp_vendor_submit_product', 'change_product_page_url');
function change_product_page_url()
{
    return wcmp_get_vendor_dashboard_endpoint_url(apply_filters('km_seller_product_manager_url', 'seller-product-manager'));
}

add_filter('wcmp_vendor_fields', 'km_vendor_fields', 10, 2);
function km_vendor_fields($args, $user_id)
{
    global $WCMp;
    $vendor = new WCMp_Vendor($user_id);

    $args["vendor_shop_owner"] = array(
        'label' => __('Vendor Shop Owner', 'dc-woocommerce-multi-vendor'),
        'type' => 'text',
        'value' => $vendor->shop_owner,
        'class' => "user-profile-fields regular-text"
    );
    $args["vendor_tagline"] = array(
        'label' => __('Shop Tagline', 'dc-woocommerce-multi-vendor'),
        'type' => 'text',
        'value' => $vendor->tagline,
        'class' => "user-profile-fields regular-text"
    );

    $args["vendor_fshop"] = array(
        'label' => __('Logo', 'dc-woocommerce-multi-vendor'),
        'type' => 'upload',
        'prwidth' => 125,
        'value' => $vendor->fshop ? $vendor->fshop : $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg',
        'class' => "user-profile-fields"
    );

    return $args;
}

/**
 *  Add Endpoint url for frontend
 */
add_filter('wcmp_endpoints_query_vars', 'km_add_product_manager_query_vars');
function km_add_product_manager_query_vars($args)
{
    $key = apply_filters('km_seller_product_manager_url', 'seller-product-manager');
    $args[$key] = array(
        'label' => __('Product Manager', 'dc-woocommerce-multi-vendor'),
        'endpoint' => apply_filters('km_seller_product_manager_url', 'seller-product-manager')
    );
    $args['shop-front'] = array(
        'label' => __('Edit Store Front', 'dc-woocommerce-multi-vendor'),
        'endpoint' => get_wcmp_vendor_settings('wcmp_store_settings_endpoint', 'vendor', 'general', 'shop-front')
    );

    $key = apply_filters('km_seller_product_manager_url', 'seller-add-product-manager');
    $args[$key] = array(
        'label' => __('Add Product', 'dc-woocommerce-multi-vendor'),
        'endpoint' => apply_filters('km_seller_add_product_manager_url', 'seller-add-product-manager')
    );

    $vkey = apply_filters('km_seller_product_manager_url', 'vendor-deal');
    $args[$vkey] = array(
        'label' => __('Add Deals', 'dc-woocommerce-multi-vendor'),
        'endpoint' => apply_filters('km_seller_add_product_manager_url', 'vendor-deal')
    );
    return $args;
}
function change_post_menu_label() {
    global $menu;
    global $submenu;
    $menu[45][0] = 'Management';
}
add_action( 'admin_menu', 'change_post_menu_label' ,110);
add_filter('wcmp_vendor_dashboard_nav', 'km_add_menu_deal');
function km_add_menu_deal($vendor_nav)
{
    $vendor_nav['store-settings']['submenu']['shop-front']['label'] = 'Edit Store Front';

    $vendor_nav['vendor-signout']['url'] = wp_logout_url(site_url('/'));

    unset($vendor_nav['store-settings']['submenu']['vendor-billing']);
//    $vendor_nav['vendor-deal'] = array(
//        'label' => __('Product Deals', 'dc-woocommerce-multi-vendor'),
//        'url' => esc_url(wcmp_get_vendor_dashboard_endpoint_url(apply_filters('km_seller_product_manager_url', 'vendor-deal'))),
//        'capability' => true,
//        'position' => 22,
//        'submenu' => array(),
//        'link_target' => '_self',
//        'nav_icon' => 'dashicons-migrate'
//    );
    return $vendor_nav;
}


