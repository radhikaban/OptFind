<?php
/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
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
 * @version     1.6.4
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $post, $product;
global $wp_query;
$show_best = false;
//if(is_page('bestseller')){
//    $show_best = true;
//}
?>
<?php if ($product->is_on_sale()) : ?>
    <?php
    $show_deal = false;
    if (isset($wp_query->query['taxonomy']) && $wp_query->query['taxonomy'] == 'product_cat') {

        if (isset($wp_query->query['term']) && $wp_query->query['term'] == 'deal-of-the-days') {
            $show_deal = true;
        }
    }elseif (get_queried_object()->term_id == 102){
        $show_deal = true;
    }
    ?>
    <?php if ($show_deal): ?>
        <?php echo apply_filters('woocommerce_sale_flash', '<span class="onsale deal_of_the_days">' . esc_html__('Deal of the days', 'woocommerce') . '</span>', $post, $product); ?>
    <?php elseif($show_best): ?>
        <?php echo apply_filters('woocommerce_sale_flash', '<span class="onsale bestseller">' . esc_html__('Bestseller', 'woocommerce') . '</span>', $post, $product); ?>
    <?php else: ?>
        <?php echo apply_filters('woocommerce_sale_flash', '<span class="onsale">' . esc_html__('Sale!', 'woocommerce') . '</span>', $post, $product); ?>
    <?php endif; ?>

<?php endif;

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
