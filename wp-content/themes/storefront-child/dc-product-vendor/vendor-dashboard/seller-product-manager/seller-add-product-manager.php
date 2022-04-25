<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/6/2017
 * Time: 4:34 PM
 */

if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMp;
$user = wp_get_current_user();
$vendor = get_wcmp_vendor($user->ID);

if ($vendor) :
    ?>
    <style>
        .wcmp_form1 .list-inline {
            margin-left: 35px;
        }

        .list-inline li {
            display: inline-block;
            padding: 0 5px;
        }
    </style>

    <script>
        jQuery(document).ready(function ($) {


            jQuery(document).on('change', '.km_product_filter', function () {

                jQuery("#km_ajax_loader").show();
                jQuery("#km_pageenumber").val(1);
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

            jQuery(document).on('change', '.km_product_filter_per_page', function () {
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

            jQuery(document).on('click', '.km_add_own_product', function ($) {
                jQuery("#km_ajax_loader").show();
                if (!jQuery(this).hasClass('disabled')) {
                    jQuery(this).addClass('disabled')
                    var obj = jQuery(this);
                    jQuery.ajax({
                        type: 'POST',
                        data: {'action': 'get_product_template', 'ID': obj.data('id'), '__nonce': obj.data('nonce')},
                        dataType: 'json',
                        url: "<?php echo admin_url('admin-ajax.php') ?>",
                        success: function (data) {
                            jQuery("#km_ajax_loader").hide();
                            obj.siblings('.km_msg').html(data.msg);
                            if (data.response == 'success') {
                                obj.siblings('.km_msg').after(data.link);
                                obj.remove();
                            }

                        }
                    });
                }


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
    <div class="wcmp_headding2"><?php _e('Add Product', 'dc-woocommerce-multi-vendor'); ?></div>
    <form id="km-shop-archive-product" method="post" name="shop_settings_form"
          class="wcmp_shop_settings_form  shop_km_form wcmp_billing_form">
        <input type="hidden" name="action" value="km_get_product_list">
        <input type="hidden" id="load_more" name="load_more" value="">
        <?php wp_nonce_field('km_get_products_list', 'km_get_products_list') ?>
        <div class="wcmp_form1">

            <p><?php _e('Product Category', 'dc-woocommerce-multi-vendor'); ?></p>

            <?php

            km_get_terms('product_cat', 'all', true, false, 0, true, false);

            ?>
            <p><?php _e('Product Brands', 'dc-woocommerce-multi-vendor'); ?></p>

            <?php

            km_get_terms('pwb-brand', 'all', true, false, 0, true, false);

            ?>

            <div class="left-block"><p><?php _e('Filter By', 'dc-woocommerce-multi-vendor'); ?></p></div>

            <div id="filterTabs">
                <?php
                $attributes = km_get_product_attributes();
                if (!empty($attributes)) {
                echo "<ul class='list-inline resp-tabs-list'>";
                foreach ($attributes as $k => $v) {
                    echo "<li>" . $v . "</li>";

                }
                echo "</ul>";
                ?>
                <div class="resp-tabs-container">
                    <?php
                    foreach ($attributes as $k => $v) {
                        echo "<div id='" . $k . "'>";
                    
                        km_get_terms($k, 'all', false, false, 0, true);
                        echo "</div>";
                    }
                    echo "</div>";
                    }


                    ?>
                </div>
            </div>
            <div id="wcmp_auto_suggest_product_title"></div>


            <select name="postperpage" class="km_product_filter_per_page">
                <option <?php echo (KM_POST_PER_PAGE == '') ? "selected" : '' ?> value="">Select Products Per Page</option>
                <option <?php echo (KM_POST_PER_PAGE == '-1') ? "selected" : '' ?> value="-1">All</option>
                <option <?php echo (KM_POST_PER_PAGE == '1') ? "selected" : '' ?> value="1">1</option>
                <option <?php echo (KM_POST_PER_PAGE == '3') ? "selected" : '' ?> value="3">3</option>
                <option <?php echo (KM_POST_PER_PAGE == '6') ? "selected" : '' ?> value="6">6</option>
                <option <?php echo (KM_POST_PER_PAGE == '9') ? "selected" : '' ?> value="9">9</option>
            </select>
            <style>
                .km_vendor_shop_dashboard {
                    position: relative;
                }

                .km_vendor_shop_dashboard .wcmp_review_loader img {
                    position: absolute;
                    left: 50%;
                    top: 50%;
                    -moz-transform: translateX(-50%) translateY(-50%);
                    -webkit-transform: translateX(-50%) translateY(-50%);
                    -o-transform: translateX(-50%) translateY(-50%);
                    -ms-transform: translateX(-50%) translateY(-50%);
                    transform: translateX(-50%) translateY(-50%);
                }

                .km_vendor_shop_dashboard .wcmp_review_loader {
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
            <div class="km_vendor_shop_dashboard">
                <?php
                echo '<div class="wcmp_review_loader" id="km_ajax_loader" ><img src="' . $WCMp->plugin_url . 'assets/images/fpd/ajax-loader.gif" alt="ajax-loader" /></div>';
                ?>
                <div id="append_data_product">
                    <?php

                    global $wpdb;
                    $dc_vendor_id = get_user_meta(get_current_user_id(), '_vendor_term_id', true);
                    $sql = "SELECT post_id  from {$wpdb->postsmeta} where meta_key='p_parent_id'";
                    $sql2 = "select DISTINCT p.product_id from {$wpdb->term_relationships} as tr JOIN " . KM_MAP_TABLE . " as p on FIND_IN_SET(tr.object_id, p.product_ids ) > 0 where tr.term_taxonomy_id={$dc_vendor_id} and p.is_active=1";
                    $result = $wpdb->get_col($sql);
                    $result2 = $wpdb->get_col($sql2);

                    $user_p_ids = array();



                    $user_p_ids = array_merge($result, $result2);
                    
                    $attributes_terms = $tax_query = array();
                    $attributes = km_get_product_attributes();
                    $taxonomies = array();


                    $s_args['tax_query'] = $tax_query;
                    $s_args['orderby'] = 'name';
                    $s_args['order'] = 'asc';

                    $s_args['posts_per_page'] = KM_POST_PER_PAGE;
                    $s_args['post_type'] = 'product';


                    $s_args['post__not_in'] = $user_p_ids;

                    $WCMp->template->get_template('archive-products.php', array('args' => $s_args));
                    ?>

                </div>
            </div>
    </form>


<?php endif; ?>