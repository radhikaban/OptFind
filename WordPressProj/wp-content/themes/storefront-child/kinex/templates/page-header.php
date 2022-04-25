<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/19/2017
 * Time: 10:50 AM
 */
?>

<?php

global $post;
$image = '';
$no_image = false;
?>



<?php if (is_product_category()) { ?>

    <?php
    global $wp_query;

    // get the query object
    $cat = $wp_query->get_queried_object();

    // get the thumbnail id using the queried category term_id
    $thumbnail_id = get_term_meta($cat->term_id, 'thumbnail_id', true);


    // get the image URL
    $image = wp_get_attachment_url($thumbnail_id);


    $title = single_term_title("", false);
} else if (is_single() && get_post_type() == 'post') {

    $post = get_page_by_path('blog');
    $title = get_the_title();

    if (has_post_thumbnail($post)) {
        $image = get_the_post_thumbnail_url($post);
    }


} else if (is_shop()) {


    $shop_page_id = wc_get_page_id('shop');
    $title = get_the_title($shop_page_id);

    $title = apply_filters('woocommerce_page_title', $title);
    if (has_post_thumbnail($shop_page_id)) {
        $image = get_the_post_thumbnail_url($shop_page_id);
    }
} else if (is_cart()) {
    $no_image = true;
//    $shop_page_id = wc_get_page_id('cart');
//    $title = get_the_title($shop_page_id);
//
//
//    if (has_post_thumbnail($shop_page_id)) {
//        $image = get_the_post_thumbnail_url($shop_page_id);
//    }
} else if (is_tax('dc_vendor_shop')) {
    $vendor = get_wcmp_vendor_by_term(get_queried_object()->term_id);
    $image = get_user_meta($vendor->id, '_vendor_banner', true);
    $title = get_user_meta($vendor->id, '_vendor_page_title', true);
} else if(has_post_thumbnail()) {
    $title = get_the_title();
    if (has_post_thumbnail()) {
        $image = get_the_post_thumbnail_url();
    }
}
if(!$image && !$no_image) {
    $image = get_stylesheet_directory_uri()."/images/default_banner.jpg";
}

if (is_page(wcmp_vendor_dashboard_page_id()) && is_user_wcmp_vendor(get_current_user_id()) || is_search()) :
    ?>
    <div class="page-header" > </div>
    <?php
else:



?>

<div class="page-header" style="background-image: url('<?php echo $image ?>')">
    <div class="col-full  <?php if (is_page('sell-with-us')): echo  'km-register-outter'; endif;?>">
        <?php
        if (is_cart()):

            if (WC()->cart->is_empty()) {
                do_action('woocommerce_cart_is_empty');
            } else {
                ?>

                   <?php
                    $total = WC()->cart->get_cart_contents_count();
                    echo "<h2 class='cart-custom-contents'>You have {$total} item in your cart.</h2>";
                 ?>
                    <div class="cart-collaterals cart-empty-fragemnts">
                        <?php
                        /**
                         * woocommerce_cart_collaterals hook.
                         *
                         * @hooked woocommerce_cross_sell_display
                         * @hooked woocommerce_cart_totals - 10
                         */
                        do_action('woocommerce_cart_collaterals');
                        ?>
                    </div>
               

                <?php
            }


        else:

            ?>
            <h1 class="page-title"><?php echo $title ?></h1>
          

        <?php endif; ?>
    </div>
</div>

<?php endif; ?>