<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/15/2017
 * Time: 4:13 PM
 */
global $WCMp;
$columns = 3;
$woocommerce_loop['columns'] = 3;
$woocommerce_loop['name'] = $loop_name = 'product_loop';

$transient_name = 'wc_loop' . substr(md5(json_encode($args) . $loop_name), 28) . WC_Cache_Helper::get_transient_version('product_query');
$products = get_transient($transient_name);

if (false === $products || !is_a($products, 'WP_Query')) {
    $products = new WP_Query($args);
    set_transient($transient_name, $products, DAY_IN_SECONDS * 30);
}

ob_start();
$page = 0;

if ($products->have_posts()) {

    $page = 0;

    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;


    if (isset($_POST['pagenumber'])) {
        $paged = $_POST['pagenumber'];
    }
//    if(isset($_POST['postperpage'])){
//        $paged =1;
//    }

    $page = $products->max_num_pages;

    // Prime caches before grabbing objects.
    update_post_caches($products->posts, array('product', 'product_variation'));


    ?>


    <?php $WCMp->template->get_template('vendor-dashboard/seller-product-manager/single-product/product-loop.php', array('products' => $products,'page'=>$page,'paged'=>$paged)); ?>


    <?php
}

woocommerce_reset_loop();
wp_reset_postdata();

echo  ob_get_clean() ;