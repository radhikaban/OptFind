<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/20/2017
 * Time: 9:56 AM
 */

/**
 * The Template for displaying products in a product category. Simply includes the archive template.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/taxonomy-dc_vendor_shop.php
 *
 * @author        WC Marketplace
 * @package    WCMp/Templates
 * @version   2.2.0
 */

global $WCMp;
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Get vendor
$vendor = get_wcmp_vendor_by_term(get_queried_object()->term_id);

if (!$vendor) {
    // Redirect if not vendor
    wp_safe_redirect(get_permalink(wc_get_page_id('shop')));
    exit();
}

$is_block = get_user_meta($vendor->id, '_vendor_turn_off', true);

if ($is_block) {
    get_header('shop');

    ?>


    <?php
    if (!is_user_logged_in() || $vendor->id != get_current_user_id()) {
        $block_vendor_desc = "This shop is currently blocked";
    } else {
        $block_vendor_desc = $WCMp->vendor_caps->frontend_cap['block_vendor_desc'];
    }


    ?>
    <p class="blocked_desc">
        <?php echo esc_attr($block_vendor_desc); ?>
    </p>

    <?php get_footer('shop');

} else {
    get_header('shop');

    // Get vendor ID
    $vendor_id = get_queried_object()->term_id;
    // Get vendor info
    $vendor = get_wcmp_vendor_by_term($vendor_id);
    $image = $vendor->image ? $vendor->image : $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
    $description = $vendor->description;

    $address = '';

    if ($vendor->city) {
        $address = $vendor->city . ', ';
    }
    if ($vendor->state) {
        $address .= $vendor->state . ', ';
    }
    if ($vendor->country) {
        $address .= $vendor->country;
    }
    $WCMp->template->get_template('archive_vendor_info.php', array('vendor_id' => $vendor->id, 'banner' => $vendor->banner, 'profile' => $image, 'description' => stripslashes($description), 'mobile' => $vendor->phone, 'location' => $address, 'email' => $vendor->user_data->user_email));

    ?>
    <style>
        .vendor-right-list {
            position: relative;
        }

        .vendor-right-list .wcmp_review_loader img {
            position: absolute;
            left: 50%;
            top: 50%;
            -moz-transform: translateX(-50%) translateY(-50%);
            -webkit-transform: translateX(-50%) translateY(-50%);
            -o-transform: translateX(-50%) translateY(-50%);
            -ms-transform: translateX(-50%) translateY(-50%);
            transform: translateX(-50%) translateY(-50%);
        }

        .vendor-right-list .wcmp_review_loader {
            width: 100%;
            position: absolute;
            z-index: 8888;
            background-color: rgba(0, 0, 0, 0.6);
            opacity: .5;
            text-align: center;
            display: none;
            height: 100%;
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
        }
    </style>
    <script>
        jQuery(document).ready(function ($) {
            jQuery(document).on('change', '.km_shop_product_sort', function () {
                jQuery("#km_ajax_loader").show();
                jQuery("#km_pageenumber").val(1),
                jQuery.ajax({
                    type: 'POST',
                    data: jQuery('form.shop_km_form').serialize(),
                    dataType: 'html',
                    url: "<?php echo admin_url('admin-ajax.php') ?>",
                    success: function (data) {
                        jQuery("#km_ajax_loader").hide();
                        jQuery('#append_data_product').html(data);
                        jQuery("#more_posts").attr("disabled", false);
                    }
                });
            })
            jQuery(document).on('click', '.km_shop_product_filter_button', function (e) {
                e.preventDefault();
                jQuery("#km_ajax_loader").show();
                jQuery("#km_pageenumber").val(1),
                    jQuery('#km_shop_product_category').val('-1'),
                    jQuery.ajax({
                        type: 'POST',
                        data: jQuery('form.shop_km_form').serialize(),
                        dataType: 'html',
                        url: "<?php echo admin_url('admin-ajax.php') ?>",
                        success: function (data) {
                            jQuery("#km_ajax_loader").hide();
                            jQuery('#append_data_product').html(data);
                            jQuery("#more_posts").attr("disabled", false);
                        }
                    });

            })

            jQuery(document).on('click', '.km_shop_product_filter', function () {
                obj = jQuery(this);
                jQuery("#km_ajax_loader").show();
                jQuery.ajax({
                    type: 'POST',
                    data: {
                        'product_cat': obj.data('id'),
                        'vendor_id': obj.data('vendor'),
                        '_nonce': obj.data('nonce'),
                        'is_taxonomy': true,
                        'action': 'km_shop_product_filter'
                        ,
                    },
                    dataType: 'html',
                    url: "<?php echo admin_url('admin-ajax.php') ?>",
                    success: function (data) {
                        jQuery("#km_ajax_loader").hide();
                        jQuery('#append_data_product').html(data);
                        jQuery("#more_posts").attr("disabled", false);
                    }
                });
            })


            jQuery(document).on('click', '#more_posts', function ($) {
                jQuery("#km_ajax_loader").show();
                jQuery("#load_more").val('true'),

                    jQuery("#more_posts").attr("disabled", true), // Disable the button, temp.
                    jQuery.ajax({
                        type: 'POST',
                        data: jQuery('form.shop_km_form').serialize(),
                        dataType: 'html',
                        url: "<?php echo admin_url('admin-ajax.php') ?>",
                        success: function (data) {
                            jQuery("#km_ajax_loader").hide();
                            jQuery('.load_more_wrap').remove(),
                                jQuery('#append_data_product  .products').append(data);
                            jQuery("#more_posts").attr("disabled", false);
                            jQuery("#load_more").val('');

                        }
                    });

            })


        });


    </script>
    </div>
    </div>


    <div class="product-sort-header">
        <div class="col-full">
            <?php
            $c_args = array(
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
            $c_query = get_posts($c_args);
            ?>
            <h4>Items (<?php echo count($c_query) ?>)</h4>
            <form id="km-shop-archive-product" class="shop_km_form">
                <select form="km-shop-archive-product" selected="selected" name="orderby" class="km_shop_product_sort">
                    <option value="menu_order">Default sorting</option>
                    <option value="popularity">Sort by popularity</option>
                    <option value="rating">Sort by average rating</option>
                    <option value="date">Sort by newness</option>
                    <option value="price">Sort by price: low to high</option>
                    <option value="price-desc">Sort by price: high to low</option>
                </select>
                <input type="hidden" name="is_taxonomy" value="true">
                <input form="km-shop-archive-product" type="text" name="shop_s"
                       value="<?php echo isset($_POST['s']) ? $_POST['s'] : '' ?>">
                <button form="km-shop-archive-product" class="km_shop_product_filter_button">Search</button>
            </form>
        </div>
    </div>

    <div class="col-full">


        <div id="append_data_product">
            <?php
            global $wpdb;

            $tax_query = array();
            $taxonomies = array();
            $tax_query[] =
                array(
                    'taxonomy' => 'dc_vendor_shop',
                    'field' => 'id',
                    'terms' => get_user_meta($vendor->id, '_vendor_term_id', true)
                );
            $args['tax_query'] = $tax_query;

            $args['posts_per_page'] = KM_POST_PER_PAGE;
            $args['post_type'] = 'product';
            $args['post_status'] = 'publish';
            wc_get_template('shop-archive-products.php', array('args' => $args, 's_vendor_id' => $vendor->id));
            ?>
        </div>


    </div>

    <?php
    $queried_object = get_queried_object();
    $WCMp->template->get_template('wcmp-vendor-review-form.php', array('queried_object' => $queried_object));
    ?>
    <?php get_footer('shop');

}
