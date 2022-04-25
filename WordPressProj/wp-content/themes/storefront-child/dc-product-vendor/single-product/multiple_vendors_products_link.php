<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/single-product/multiple_vendors_products_link.php.
 *
 * HOWEVER, on occasion WCMp will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 *
 * @author  WC Marketplace
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 2.3.4
 */
global $WCMp, $post, $wpdb;
$results_str = '';
$searchstr = $post->post_title;

if (isset($_REQUEST['vendor']) && !empty($_REQUEST['vendor']) && wc_get_product($_REQUEST['vendor']) && get_post_status($_REQUEST['vendor']) == 'publish') {
    $p_parent_id = get_post_meta($post->ID, 'p_parent_id', true);
    $querystr = "select * from " . KM_MAP_TABLE . " where product_id = '{$p_parent_id}'";
} else {
    $querystr = "select * from " . KM_MAP_TABLE . " where product_id = '{$post->ID}'";
}


$results_obj_arr = $wpdb->get_results($querystr);



if (isset($results_obj_arr) && count($results_obj_arr) > 0) {
    $results_str = $results_obj_arr[0]->product_ids;
}

if (!empty($results_str)) {
    $product_id_arr = explode(',', $results_str);

    if (isset($_GET['vendor'])) {
        if (($key = array_search($_GET['vendor'], $product_id_arr)) !== false) {
            unset($product_id_arr[$key]);
        }
    }
    if (count($product_id_arr) > 0) {
        $args = array(
            'posts_per_page' => -1,
            'offset' => 0,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => 'product',
            'post__in' => $product_id_arr,
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $results = get_posts($args);


        if (count($results) > 0) {
            $button_text = apply_filters('wcmp_more_vendors', __('More Vendors', 'dc-woocommerce-multi-vendor'));
            echo '<a  href="#" class="goto_more_offer_tab button">' . $button_text . '</a>';
        }
    }

}

