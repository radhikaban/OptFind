<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/7/2017
 * Time: 2:27 PM
 */

if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}


global $WCMp,$product;
$user = wp_get_current_user();
$vendor = get_wcmp_vendor($user->ID);
$product = wc_get_product($product_detail);
if ($product->is_type('variable')) {
    $available_variations = $product->get_available_variations();
    foreach ($available_variations as $key => $value) {
        // debug($value, false);
    }
}
if ($vendor && $product) :

    ?>
    <div class="wcmp_headding2"><?php _e('Product Detail', 'dc-woocommerce-multi-vendor'); ?></div>
    <form method="post" action="" name="product_save_data" class="wcmp_shop_settings_form wcmp_billing_form">
        <div class="wcmp_form1 row">
            <div class="col-sm-6">
                <p><?php _e('Product Title', 'dc-woocommerce-multi-vendor'); ?></p>
            </div>
            <div class="col-sm-6">
                <input class="no_input" type="hidden" name="product_manager[product_id]"
                value="<?php echo $product_detail ?>"
                placeholder="<?php _e('Enter your Product name', 'dc-woocommerce-multi-vendor'); ?>">

                <input class="no_input" type="text" name="product_manager[product_name]"
                value="<?php echo ($product->get_title()) ? $product->get_title() : ''; ?>"
                placeholder="<?php _e('Enter your Product name', 'dc-woocommerce-multi-vendor'); ?>">

            </div>
        </div>
        <?php

        if ($product->is_type('variable')) {
            ?>
            <p><?php _e('Product Variations', 'dc-woocommerce-multi-vendor'); ?></p>
            <input class="no_input" type="hidden" name="product_manager[is_variable]"
            value="yes">
            <?php

            $available_variations = $product->get_available_variations();
            foreach ($available_variations as $key => $value) {
                $attr_title = array();
                $attr_name = array();

                foreach ($value['attributes'] as $a_key => $val) {

                    preg_match('/attribute_(.*)/', $a_key, $matches);
                    $meta = get_post_meta($value['variation_id'], $a_key, true);
                    $term = get_term_by('slug', $meta, $matches[1]);

                    $tax = get_taxonomy($matches[1]);
                    $attr_name[$matches[1]] = $meta;
                    $attr_title[] = $tax->label . ":" . $term->name;
                    ?>
                    <input class="no_input" type="hidden" name="product_manager[available_attributes][]"
                    value="<?php echo $tax->name ?>">
                    <?php

                }


                ?>

                <div class="half_part clearfix">
                    <p><?php _e(implode("|", $attr_title), 'dc-woocommerce-multi-vendor'); ?></p>

                    <?php foreach ($attr_name as $v_key => $v_val): ?>
                        <?php

                        ?>
                        <input class="no_input" type="hidden"
                        name="product_manager[variations][<?php echo $key ?>][attributes][<?php echo $v_key ?>]"
                        value="<?php echo $v_val ?>">
                    <?php endforeach; ?>


                </div>

                <div class="half_part">

                    <input class="no_input" type="hidden"
                    name="product_manager[variations][<?php echo $key ?>][variation_id]"
                    value="<?php echo $value['variation_id'] ?>" placeholder="Enter your variation price">
                    <input class="no_input"  type="text"
                    name="product_manager[variations][<?php echo $key ?>][regular_price]"
                    value="<?php echo $variable_product1->get_regular_price() ?>" placeholder="Enter your variation Regular price">
                    <input class="no_input"  type="text"
                    name="product_manager[variations][<?php echo $key ?>][sale_price]"
                    value="<?php echo $variable_product1->get_sale_price() ?>" placeholder="Enter your variation Sale price">


                </div>

                <div class="action_div_space"></div>
                <?php
                    //echo $key."---".debug($value,false);
            }

        } else {
            ?>
            <p><?php _e('Regular Price', 'dc-woocommerce-multi-vendor'); ?></p>

            <input class="no_input"  type="text" name="product_manager[regular_price]"
            value="<?php echo ($product->get_regular_price()) ? $product->get_regular_price() : ''; ?>"
            placeholder="<?php _e('Enter your regular price', 'dc-woocommerce-multi-vendor'); ?>">
            <p><?php _e('Sale Price', 'dc-woocommerce-multi-vendor'); ?></p>

            <input class="no_input"  type="text" name="product_manager[sale_price]"
            value="<?php echo ($product->get_sale_price()) ? $product->get_sale_price() : ''; ?>"
            placeholder="<?php _e('Enter your sale price', 'dc-woocommerce-multi-vendor'); ?>">
            <?php
        }

        ?>



        <?php do_action('km_woocommerce_product_data_panels',$product,false); ?>
        <div class="action_div">
            <button type="submit" class="wcmp_orange_btn"
            name="store_save"><?php _e('Save Options', 'dc-woocommerce-multi-vendor'); ?></button>
            <div class="clear"></div>
        </div>
    </form>
<?php elseif ($vendor): ?>
    <div class="wcmp_headding2"><?php _e('Product does not exist', 'dc-woocommerce-multi-vendor'); ?></div>
    <?php

    //$user_array = $WCMp->user->get_vendor_fields($vendor->id);
    //$WCMp->template->get_template('vendor-dashboard/seller-product-manager/seller-add-product-manager.php', $user_array);
    ?>
<?php endif; ?>