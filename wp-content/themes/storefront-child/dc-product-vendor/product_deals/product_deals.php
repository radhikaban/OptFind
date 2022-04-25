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

    </script>
    <div class="wcmp_headding2"><?php _e('Add deals', 'dc-woocommerce-multi-vendor'); ?></div>
    <form method="post" name="shop_settings_form" class="wcmp_shop_settings_form wcmp_billing_form">
        <input type="hidden" name="action" value="km">
        <input type="hidden" id="load_more" name="load_more" value="">
        <?php wp_nonce_field('km_get_products_list', 'km_get_products_list') ?>
    </form>


<?php endif; ?>