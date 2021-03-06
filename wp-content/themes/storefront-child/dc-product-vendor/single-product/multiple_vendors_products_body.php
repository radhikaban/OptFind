<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/single-product/multiple_vendors_products_body.php.
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

if (!defined('ABSPATH')) {
    exit;
}
global $WCMp;
if (isset($more_product_array) && is_array($more_product_array) && count($more_product_array) > 0) {
    if (isset($sorting) && !empty($sorting)) {
        /*function wcmp_sort_by_price($a, $b) {
            return $a['price_val'] - $b['price_val'];
        }*/
        if ($sorting == 'price') {
            usort($more_product_array, function ($a, $b) {
                return $a['price_val'] - $b['price_val'];
            });
        } elseif ($sorting == 'price_high') {
            usort($more_product_array, function ($a, $b) {
                return $a['price_val'] - $b['price_val'];
            });
            $more_product_array = array_reverse($more_product_array);
        } elseif ($sorting == 'rating') {
            $more_product_array = wcmp_sort_by_rating_multiple_product($more_product_array);
        } elseif ($sorting == 'rating_low') {
            $more_product_array = wcmp_sort_by_rating_multiple_product($more_product_array);
            $more_product_array = array_reverse($more_product_array);
        }
    }
    foreach ($more_product_array as $more_product) {
        $_product = wc_get_product($more_product['product_id']);
        ?>
        <div class="row rowbody">
            <div class="rowsub ">
                <div class="vendor_name">
                    <a href="<?php echo $more_product['shop_link']; ?>"
                       class="wcmp_seller_name"><?php echo $more_product['seller_name']; ?></a>
                    <?php do_action('after_wcmp_singleproductmultivendor_vendor_name', $more_product['product_id'], $more_product); ?>
                </div>
                <?php
                if (isset($more_product['rating_data']) && is_array($more_product['rating_data']) && isset($more_product['rating_data']['avg_rating']) && $more_product['rating_data']['avg_rating'] != 0 && $more_product['rating_data']['avg_rating'] != '') {
                    echo wc_get_rating_html($more_product['rating_data']['avg_rating']);
                } else {
                    echo "<div class='star-rating'></div>";
                }
                ?>
            </div>
            <div class="rowsub">
                <?php echo $_product->get_price_html(); ?>
            </div>
            <div class="rowsub">
                <?php
                $vendor = get_wcmp_product_vendors($_product->get_id());
                $vendor_address = array();
                $vendor_address[] = get_user_meta($vendor->id, '_vendor_page_title', true);
                $vendor_address[] = get_user_meta($vendor->id, '_vendor_address_1', true);
                $vendor_address[] = get_user_meta($vendor->id, '_vendor_address_2', true);
                $vendor_address[] = get_user_meta($vendor->id, '_vendor_city', true);
                $vendor_address[] = get_user_meta($vendor->id, '_vendor_state', true);
                $vendor_address[] = get_user_meta($vendor->id, '_vendor_postcode', true);
                $vendor_address[] = get_user_meta($vendor->id, '_vendor_country', true);

                $vendor_address = array_filter($vendor_address);

                if (count($vendor_address) > 0):
                    $term = implode(", ", $vendor_address);
                    ?>
                    <a href="https://www.google.com/maps?q=<?php echo $term ?>"><?php echo $term ?></a>
                <?php endif; ?>
            </div>
            <div class="rowsub">
                <?php
                $kmcl_addon_lenses = get_post_meta($more_product['product_id'], 'kmcl_addon_lenses', true);
                ?>
                <?php if ($more_product['product_type'] == 'simple' && $kmcl_addon_lenses != 'yes') { ?>
                    <a href="<?php echo '?add-to-cart=' . $more_product['product_id']; ?>"
                       class="buttongap button"><?php echo apply_filters('add_to_cart_text', __('Add to Cart', 'dc-woocommerce-multi-vendor')); ?></a>
                <?php } ?>
                <a href="<?php echo get_permalink($more_product['product_id']); ?>"
                   class="buttongap button"><?php echo __('Details', 'dc-woocommerce-multi-vendor'); ?></a>
            </div>
            <div style="clear:both;"></div>
        </div>


        <?php
    }
}
?>