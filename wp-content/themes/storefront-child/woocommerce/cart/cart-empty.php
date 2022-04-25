<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/22/2017
 * Time: 11:54 AM
 */

wc_print_notices();

/**
 * @hooked wc_empty_cart_message - 10
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

wc_print_notices();


if (wc_get_page_id('shop') > 0) : ?>
    <p class="return-to-shop">
        <a class="button wc-backward"
           href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', wc_get_page_permalink('shop'))); ?>">
            <?php _e('Return to shop', 'woocommerce') ?>
        </a>
    </p>
<?php endif; ?>
