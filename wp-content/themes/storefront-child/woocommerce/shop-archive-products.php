<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/20/2017
 * Time: 12:27 PM
 */


if ($s_vendor_id && !empty($s_vendor_id)) {
    $vendor = get_wcmp_vendor($s_vendor_id);
} else {
    $vendor = get_wcmp_vendor_by_term(get_queried_object()->term_id);
}

?>

<div class="vendor-left-sidebar">
    <ul class="list_category">

        <?php


        $nonce = wp_create_nonce('km_shop_product_filter');
        $t_args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page'=>'-1',
            'tax_query' => array(
                array(
                    'taxonomy' => 'dc_vendor_shop',
                    'field' => 'id',
                    'terms' => get_user_meta($vendor->id, '_vendor_term_id', true)
                )
            )
        );

        $my_query = get_posts($t_args);

        ?>
        <li class="<?php echo (!isset($_POST['product_cat']) || $_POST['product_cat'] == '-1') ? 'active' : '' ?>">
            <a href='javascript:void(0)' data-nonce="<?php echo $nonce ?>" data-id="-1"
               class='km_shop_product_filter' data-vendor="<?php echo $vendor->id  ?>">  All <span><?php echo count($my_query) ?> </span></a></li>

        <?php
        $t_args = array(
            'hide_empty' => false,
        );
        $product_categories = get_terms('product_cat', $t_args);

        foreach ($product_categories as $cat) {

            global $wpdb;
            $t_args = array(
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page'=>'-1',
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'dc_vendor_shop',
                        'field' => 'id',
                        'terms' => get_user_meta($vendor->id, '_vendor_term_id', true)
                    ),
                    array(
                        'taxonomy' => $cat->taxonomy,
                        'field' => 'slug',
                        'terms' => array($cat->slug),
                        'operator' => 'IN'
                    )
                )
            );
            $my_query = get_posts($t_args);

            if (count($my_query) > 0) {
                $active = (isset($_POST['product_cat']) && $_POST['product_cat'] == $cat->slug) ? "active" : "";
                echo "<li class='" . $active . "' ><a href='javascript:void(0)' data-vendor='" . $vendor->id . "' data-nonce='" . $nonce . "' data-id='" . $cat->slug . "' class='km_shop_product_filter'>" . $cat->name . ' <span>' . count($my_query) . '</span></a></li>';
            }

        }

        ?>


    </ul>
    <div class="vendor-widget-block"><a
                href="mailto:<?php echo apply_filters('vendor_shop_page_email', $vendor->user_data->user_email, $vendor->id ); ?>"
                class="btn btn-primary">Contact Shop Owner</a></div>
    <div class="vendor-widget-block"><p><?php do_action('km_get_total_order_shop', $vendor->id); ?></p></div>
    <input form="km-shop-archive-product" type="hidden" name="action"
           value="km_shop_product_filter">
    <input form="km-shop-archive-product" type="hidden" id="km-shop-product-vendor" name="vendor_id"
           value="<?php echo $vendor->id ?>">
    <input form="km-shop-archive-product" type="hidden" name="action"
           value="km_shop_product_filter">
    <input form="km-shop-archive-product" type="hidden" id="km_shop_product_category" name="product_cat"
           value="<?php echo isset($_POST['product_cat']) && is_numeric($_POST['product_cat']) ? $_POST['product_cat'] : '-1' ?>">
    <input form="km-shop-archive-product" type="hidden" name="_nonce"
           value="<?php echo $nonce ?>">
    <input form="km-shop-archive-product" id="load_more" type="hidden" name="load_more"
           value="">
</div>
<div class="vendor-right-list">

    <?php
    global $WCMp;

    echo '<div class="wcmp_review_loader" id="km_ajax_loader" ><img src="' . $WCMp->plugin_url . 'assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>';
    $WCMp->template->get_template('archive-products.php', array('args' => $args));
    ?>

</div>

<div style="clear: both"></div>