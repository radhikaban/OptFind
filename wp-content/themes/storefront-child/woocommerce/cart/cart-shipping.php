<?php
/**
 * Shipping Methods Display
 *
 * In 2.1 we show methods per package. This allows for multiple methods per order if so desired.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-shipping.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     3.2.0
 */
if (!defined('ABSPATH')) {
    exit;
}


$product_id = $package['contents'][0]['product_id'];
if (isset($package['contents'][0]['variation_id']) && $package['contents'][0]['variation_id'] != 0) {
    $product_id = $item['variation_id'];
}
?>
<tr class="shipping" xmlns="http://www.w3.org/1999/html">


    <?php
    $product_in_cart = false;
    $new_methods = '';
    ?>
    <?php foreach ($available_methods as $key => $method) : ?>
        <?php


        $terms = get_the_terms($product_id, 'product_cat');
        if ($terms) {
            foreach ($terms as $term) {

                $_categoryid = $term->term_id;
                if ($_categoryid === 70 && $method->method_id == "local_pickup") {

                    $product_in_cart = true;
                    $new_methods = $method->method_id;
                    break;
                } elseif ($_categoryid === 104 && $method->method_id == "free_shipping") {
                    $new_methods = $method->method_id;
                    $product_in_cart = true;
                    break;
                }
            }

        }
        ?>
    <?php endforeach; ?>
    <?php $new_available_methods = $available_methods; ?>
    <?php if ($product_in_cart): ?>

        <?php foreach ($new_available_methods as $key => $method) : ?>

            <?php
            if ($method->method_id != $new_methods) {
                unset($available_methods[$key]);
            }
            ?>
        <?php endforeach; ?>
    <?php endif; ?>


    <th>
        <?php echo wp_kses_post($package_name); ?>

        <?php $vendor = get_wcmp_product_vendors($product_id); ?>
        <?php if ($vendor && $new_methods != 'free_shipping'): ?>


            <ul class="variation">
                <li class="variation-SoldBy">
                    <strong>Sold By:</strong> <?php echo $vendor->user_data->display_name; ?>
                </li>
                <li>
                    <strong>Vendor Pickup Address</strong>
                    <?php
                    $vendor_address = array();
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_page_title', true);

                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_address_1', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_address_2', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_city', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_state', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_postcode', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_country', true);

                    $vendor_address = array_filter($vendor_address);

                    echo implode(", ", $vendor_address);
                    ?>
                </li>

            </ul>
        <?php elseif ($new_methods == 'free_shipping') : ?>
        <ul class="variation">
            <li class="variation-SoldBy">
                Accessories will be shipped directly to customer
            </li>
        </ul>

        <?php endif; ?>


    </th>
    <td data-title="<?php echo esc_attr($package_name); ?>">
        <?php if (1 < count($available_methods)) : ?>
            <ul id="shipping_method">


                <?php foreach ($available_methods as $method) : ?>

                    <?php


                    if (1 === count($available_methods)) {

                        ?>
                        <?php
                        printf('<input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />
								<label for="shipping_method_%1$d_%2$s">%5$s</label>',
                            $index, sanitize_title($method->id), esc_attr($method->id), checked($method->id, $chosen_method, false), wc_cart_totals_shipping_method_label($method));

                        do_action('woocommerce_after_shipping_rate', $method, $index);
                        ?>
                        <?php
                        break;
                    } else {

                        if ($method->method_id == 'free_shipping') continue;
                        ?>


                        <li>
                            <?php
                            printf('<input type="radio" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d_%2$s" value="%3$s" class="shipping_method" %4$s />
								<label for="shipping_method_%1$d_%2$s">%5$s</label>',
                                $index, sanitize_title($method->id), esc_attr($method->id), checked($method->id, $chosen_method, false), wc_cart_totals_shipping_method_label($method));

                            do_action('woocommerce_after_shipping_rate', $method, $index);
                            ?>
                        </li>
                        <?php
                    }

                    ?>
                <?php endforeach; ?>
            </ul>
        <?php elseif (1 === count($available_methods)) : ?>
            <?php
            $method = current($available_methods);
            printf('%3$s <input type="hidden" name="shipping_method[%1$d]" data-index="%1$d" id="shipping_method_%1$d" value="%2$s" class="shipping_method" />', $index, esc_attr($method->id), wc_cart_totals_shipping_method_label($method));
            do_action('woocommerce_after_shipping_rate', $method, $index);
            ?>
        <?php elseif (WC()->customer->has_calculated_shipping()) : ?>
            <?php echo apply_filters(is_cart() ? 'woocommerce_cart_no_shipping_available_html' : 'woocommerce_no_shipping_available_html', wpautop(__('There are no shipping methods available. Please ensure that your address has been entered correctly, or contact us if you need any help.', 'woocommerce'))); ?>
        <?php elseif (!is_cart()) : ?>
            <?php echo wpautop(__('Enter your full address to see shipping costs.', 'woocommerce')); ?>
        <?php endif; ?>

        <?php if ($show_package_details) : ?>
            <?php echo '<p class="woocommerce-shipping-contents"><small>' . esc_html($package_details) . '</small></p>'; ?>
        <?php endif; ?>

        <?php if (!empty($show_shipping_calculator)) : ?>
            <?php woocommerce_shipping_calculator(); ?>
        <?php endif; ?>
    </td>
</tr>
