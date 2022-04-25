<?php
/**
 * The template for displaying archive vendor info
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/archive_vendor_info.php
 *
 * @author        WC Marketplace
 * @package    WCMp/Templates
 * @version   2.2.0
 */

global $WCMp;
$vendor_hide_address = get_user_meta($vendor_id, '_vendor_hide_address', true);
$vendor_hide_phone = get_user_meta($vendor_id, '_vendor_hide_phone', true);
$vendor_hide_email = get_user_meta($vendor_id, '_vendor_hide_email', true);
$tagline = get_user_meta($vendor_id, '_vendor_tagline', true);
$vendor_fshop = get_user_meta($vendor_id, '_vendor_fshop', true);
$vendor_shop_owner = get_user_meta($vendor_id, '_vendor_shop_owner', true);
$vendorshop_owner = get_user_meta($vendor_id, '_vendor_page_title', true);
?>
<div class="vendor_top_detail">
    <div class="vendor_top-left">
        <div class="vendor_description_background">
            <div class="vendor_description">


                <div class="vendor_img_add">
                    <div class="img_div"><img height="400" width="200" src="<?php echo $profile; ?>"/></div>
                </div>
                <div class="vendor_desc-wrap">
                    <h2> <?php echo $vendorshop_owner ?></h2>

                    <p><?php echo $tagline ?></p>

                    <div class="vendor_address">
                        <?php if (!empty($location) && $vendor_hide_address != 'Enable') { ?><p><i
                            class="fa fa-map-marker"></i><label><?php echo apply_filters('vendor_shop_page_location', $location, $vendor_id); ?></label>
                        </p><?php } ?>
                        <?php if (!empty($mobile) && $vendor_hide_phone != 'Enable') { ?><p><i
                            class="fa fa-phone"></i><label><?php echo apply_filters('vendor_shop_page_contact', $mobile, $vendor_id); ?></label>
                        </p><?php } ?>

                        <div class="vendor_shop_sales">

                            <?php
                            do_action('km_get_total_order_shop', $vendor_id);
                            ?>
                        </div>

                        <p class="date_since"><?php
                        $udata = get_userdata($vendor_id);

                        $registered = $udata->data->user_registered;


                        printf('On Optical find since %s', date("F Y", strtotime($registered)));


                        if (get_wcmp_vendor_settings('is_sellerreview', 'general') == 'Enable') {
                            $queried_object = get_queried_object();
                            if (isset($queried_object->term_id) && !empty($queried_object)) {
                                $rating_val_array = wcmp_get_vendor_review_info($queried_object->term_id);
                                $WCMp->template->get_template('review/rating.php', array('rating_val_array' => $rating_val_array));
                            }
                        }


                        ?>
                    </p>

                    <?php $is_vendor_add_external_url_field = apply_filters('is_vendor_add_external_url_field', true);
                    if ($WCMp->vendor_caps->vendor_capabilities_settings('is_vendor_add_external_url') && $is_vendor_add_external_url_field) {
                        $external_store_url = get_user_meta($vendor_id, '_vendor_external_store_url', true);
                        $external_store_label = get_user_meta($vendor_id, '_vendor_external_store_label', true);
                        if (empty($external_store_label)) $external_store_label = __('External Store URL', 'dc-woocommerce-multi-vendor');
                        if (isset($external_store_url) && !empty($external_store_url)) {
                            ?><p class="external_store_url"><label><a style="color: white;" target="_blank"
                              href="<?php echo apply_filters('vendor_shop_page_external_store', esc_url_raw($external_store_url), $vendor_id); ?>"><?php echo $external_store_label; ?></a></label>
                              </p><?php
                          }
                      }
                      ?>
                  </div>
                  <div class="shop-btn-action">
                    <?php do_action('km_get_count_shop_favourites', $vendor_id); ?>


                    <?php
                    if (get_current_user_id() != $vendor_id && !is_user_wcmp_vendor(get_current_user_id()) && !current_user_can('edit_post')) {
                        do_action('km_get_favourite_shop_button', $vendor_id);
                    }


                    ?>
                </div>


            </div>
        </div>
    </div>
</div>
<div class="vendor_top-right">

    <p>Shop Owner</p>


    <?php
    global $WCMp;
    $vendor_fshop = trim($vendor_fshop) ? $vendor_fshop : $WCMp->plugin_url . 'assets/images/WP-stdavatar.png';
    ?>
    <div class="shop-owner-img"><img height="400" width="200" src="<?php echo $vendor_fshop; ?>"></div>
    <h4> <?php echo $vendor_shop_owner ?></h4>
    <?php if (!empty($email) && $vendor_hide_email != 'Enable') { ?><p>

        <a
        href="mailto:<?php echo apply_filters('vendor_shop_page_email', $email, $vendor_id); ?>"> <i
        class="fa fa-envelope"></i> Contact</a>
    </p><?php } ?>
    <div class="description">
        <div class="social_profile">
            <?php
            $vendor_fb_profile = get_user_meta($vendor_id, '_vendor_fb_profile', true);
            $vendor_twitter_profile = get_user_meta($vendor_id, '_vendor_twitter_profile', true);
            $vendor_linkdin_profile = get_user_meta($vendor_id, '_vendor_linkdin_profile', true);
            $vendor_google_plus_profile = get_user_meta($vendor_id, '_vendor_google_plus_profile', true);
            $vendor_youtube = get_user_meta($vendor_id, '_vendor_youtube', true);
            $vendor_instagram = get_user_meta($vendor_id, '_vendor_instagram', true);
            ?>
            <?php if ($vendor_fb_profile) { ?> <a target="_blank"
            href="<?php echo esc_url($vendor_fb_profile); ?>">
            <img src="<?php echo $WCMp->plugin_url . 'assets/images/facebook.png'; ?>"
            alt="facebook"
            height="20" width="20"></a><?php } ?>
            <?php if ($vendor_twitter_profile) { ?> <a target="_blank"
            href="<?php echo esc_url($vendor_twitter_profile); ?>">
            <img
            src="<?php echo $WCMp->plugin_url . 'assets/images/twitter.png'; ?>"
            alt="twitter"
            height="20" width="20"></a><?php } ?>
            <?php if ($vendor_linkdin_profile) { ?> <a target="_blank"
            href="<?php echo esc_url($vendor_linkdin_profile); ?>">
            <img
            src="<?php echo $WCMp->plugin_url . 'assets/images/linkedin.png'; ?>"
            alt="linkedin"
            height="20" width="20"></a><?php } ?>
            <?php if ($vendor_google_plus_profile) { ?> <a target="_blank"
            href="<?php echo esc_url($vendor_google_plus_profile); ?>">
            <img src="<?php echo $WCMp->plugin_url . 'assets/images/google-plus.png'; ?>"
            alt="google_plus"
            height="20" width="20"></a><?php } ?>
            <?php if ($vendor_youtube) { ?> <a target="_blank"
            href="<?php echo esc_url($vendor_youtube); ?>">
            <img
            src="<?php echo $WCMp->plugin_url . 'assets/images/youtube.png'; ?>"
            alt="youtube"
            height="20" width="20"></a><?php } ?>
            <?php if ($vendor_instagram) { ?> <a target="_blank"
            href="<?php echo esc_url($vendor_instagram); ?>">
            <img src="<?php echo $WCMp->plugin_url . 'assets/images/instagram.png'; ?>"
            alt="instagram"
            height="20" width="20"></a><?php } ?>
        </div>
    </div>
</div>
</div>

<div class="clear"></div>
</div>
<div class="tabs-wrapper-main">
    <div class="col-full">
        <div id="horizontalTabSlick">
            <ul class="resp-tabs-list">
                <?php
                $c_args = array(
                    'post_type' => 'product',
                    'post_status' => 'publish',
                    'tax_query' => array(

                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'product_visibility',
                            'field' => 'name',
                            'terms' => 'featured',
                        ),
                        array(
                            'taxonomy' => 'dc_vendor_shop',
                            'field' => 'id',
                            'terms' => get_user_meta($vendor_id, '_vendor_term_id', true)
                        )
                    )
                );
                $c_query = get_posts($c_args);
                ?>
                <li>Featured Products</li>
                <li>About</li>
                <li>Policies</li>
            </ul>
            <div class="resp-tabs-container">
                <div class="site-main">
                    <div class="columns-3">
                        <?php
                        $args = array(
                            'post_type' => 'product',
                            'meta_key' => 'total_sales',
                            'orderby' => 'meta_value_num',
                            'posts_per_page' => -1,
                            'tax_query' => array(
                                'relation' => 'AND',
                                array(
                                    'taxonomy' => 'dc_vendor_shop',
                                    'field' => 'id',
                                    'terms' => get_user_meta($vendor_id, '_vendor_term_id', true)
                                ),
                                array(
                                    'taxonomy' => 'product_visibility',
                                    'field' => 'name',
                                    'terms' => 'featured',
                                ),
                            )
                        );
                        $loop = new WP_Query($args);
                        if ($loop->have_posts()): ?>
                        <div class="woocommerce">
                            <ul class='products km_feature_vendor'>
                                <?php
                                while ($loop->have_posts()) : $loop->the_post();
                                    global $product; ?>
                                    <li class="product">
                                        <a id="id-<?php the_id(); ?>" href="<?php the_permalink(); ?>"
                                           title="<?php the_title(); ?>">

                                           <?php if (has_post_thumbnail($loop->post->ID))
                                           echo get_the_post_thumbnail($loop->post->ID, 'shop_catalog');
                                           else echo '<img src="' . wc_placeholder_img_src() . '" alt="product placeholder Image" width="65px" height="115px" />'; ?>

                                           <h3><?php the_title(); ?></h3>
                                           <?php

                                           wc_get_template('loop/price.php');

                                           ?>

                                       </a>
                                   </li>
                               <?php endwhile; ?>

                           </ul>


                       </div>
                   <?php else: ?>
                    <h2>No product found for seller</h2>
                <?php endif; ?>

                <?php wp_reset_query(); ?>
            </div>
            <script>
                jQuery(document).ready(function () {

                    var slickinit;

                    jQuery('#horizontalTabSlick').easyResponsiveTabs({
                                type: 'default', //Types: default, vertical, accordion
                                width: '768px', //auto or any width like 600px
                                fit: true,   // 100% fit in a container
                                closed: 'accordion', // Start closed if in accordion view
                                activate: function (event) { // Callback function if tab is switched
                                    var $tab = jQuery(this);
                                    var $info = jQuery('#tabInfo');
                                    var $name = jQuery('span', $info);
                                    $name.text($tab.text());
                                    $info.show();



                                    slickinit.slick('unslick').slick({
                                        infinite: true,
                                        speed: 300,
                                        slidesToShow: 3,
                                        slidesToScroll: 1,
                                        prevArrow:"<i class='fa fa-chevron-right'></i>",
                                        nextArrow:"<i class='fa fa-chevron-left'></i>",

                                        responsive: [
                                        {
                                            breakpoint: 1024,
                                            settings: {
                                                slidesToShow: 3,
                                                slidesToScroll: 3,


                                            }
                                        },
                                        {
                                            breakpoint: 600,
                                            settings: {
                                                slidesToShow: 2,
                                                slidesToScroll: 2
                                            }
                                        },
                                        {
                                            breakpoint: 480,
                                            settings: {
                                                slidesToShow: 1,
                                                slidesToScroll: 1
                                            }
                                        }

                                        ]
                                    });

                                }
                            });

                    slickinit = jQuery('.km_feature_vendor').slick({
                        infinite: true,
                        speed: 300,
                        slidesToShow: 3,
                        slidesToScroll: 1,
                        prevArrow:"<i class='fa fa-chevron-right'></i>",
                        nextArrow:"<i class='fa fa-chevron-left'></i>",
                        responsive: [
                        {
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 3,


                            }
                        },
                        {
                            breakpoint: 600,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2
                            }
                        },
                        {
                            breakpoint: 480,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }

                        ]
                    });


                })
            </script>
        </div>
        <div>

            <?php

            $vendor_hide_description = get_user_meta($vendor_id, '_vendor_hide_description', true);
            if (!$vendor_hide_description) { ?>
            <div class="description_data">
                <?php
                $string = $description;
                ?>

                <?php echo stripslashes($string); ?>

            </div>

            <?php } ?>
        </div>
        <div>
            <?php
            global $WCMp;

            $vendor = new WCMp_Vendor($vendor_id);

            $wcmp_policy_settings = get_option("wcmp_general_policies_settings_name");
            $wcmp_capabilities_settings_name = get_option("wcmp_general_policies_settings_name");
            $can_vendor_edit_policy_tab_label_field = apply_filters('can_vendor_edit_policy_tab_label_field', true);
            $can_vendor_edit_cancellation_policy_field = apply_filters('can_vendor_edit_cancellation_policy_field', true);
            $can_vendor_edit_refund_policy_field = apply_filters('can_vendor_edit_refund_policy_field', true);
            $can_vendor_edit_shipping_policy_field = apply_filters('can_vendor_edit_shipping_policy_field', true);
            ?>
            <div class="policies">
                <div class="policy-tab"> <?php if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($wcmp_policy_settings['is_refund_on']) && isset($wcmp_capabilities_settings_name['can_vendor_edit_refund_policy']) && $can_vendor_edit_refund_policy_field) { ?>
                    <h4> <?php _e('Refund Policy', 'dc-woocommerce-multi-vendor'); ?>    </h4>
                    <p><?php echo ($vendor->refund_policy) ? $vendor->refund_policy : $wcmp_policy_settings['refund_policy']; ?></p>
                    <?php } ?></div>
                    <div class="policy-tab"><?php if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($wcmp_policy_settings['is_shipping_on']) && isset($wcmp_capabilities_settings_name['can_vendor_edit_shipping_policy']) && $can_vendor_edit_shipping_policy_field) { ?>
                        <h4> <?php _e('Shipping Policy', 'dc-woocommerce-multi-vendor'); ?></h4>
                        <p><?php echo ($vendor->shipping_policy) ? $vendor->shipping_policy : $wcmp_policy_settings['shipping_policy']; ?></p>
                        <?php } ?></div>
                        <div class="policy-tab"><?php if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable' && isset($wcmp_policy_settings['is_cancellation_on']) && isset($wcmp_capabilities_settings_name['can_vendor_edit_cancellation_policy']) && $can_vendor_edit_cancellation_policy_field) { ?>
                            <h4> <?php _e('Cancellation/Return/Exchange Policy', 'dc-woocommerce-multi-vendor'); ?>    </h4>
                            <p><?php echo ($vendor->cancellation_policy) ? $vendor->cancellation_policy : $wcmp_policy_settings['cancellation_policy']; ?></p>
                            <?php } ?></div>

                        </div>
                    </div>
                </div>
            </div>






