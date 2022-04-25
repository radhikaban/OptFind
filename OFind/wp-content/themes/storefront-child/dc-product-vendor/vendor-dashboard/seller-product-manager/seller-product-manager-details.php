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


global $WCMp;
$user = wp_get_current_user();
$vendor = get_wcmp_vendor($user->ID);

$ven_id = get_user_meta($user->ID, '_vendor_term_id', true);

$terms = wp_get_object_terms($product_detail, 'dc_vendor_shop');

$show_ven = false;
foreach ($terms as $term) {
    if ($term->term_id == $ven_id) {
        $show_ven = true;
        break;
    }
}


if ($show_ven):
    $product = wc_get_product($product_detail);

    if ($vendor && $product) :

        ?>
        <div class="wcmp_headding2"><?php _e('Product Detail', 'dc-woocommerce-multi-vendor'); ?></div>
        <form method="post" action="" name="product_save_data" class="wcmp_shop_settings_form wcmp_billing_form">
            <div class="wcmp_form1 vendor--product-management">
                <div class="row">
                    <div class="col-md-12">

                        <label><?php _e('Product Title', 'dc-woocommerce-multi-vendor'); ?>

                            <input class="no_input" readonly type="hidden" name="product_manager[product_id]"
                            value="<?php echo $product_detail ?>"
                            placeholder="<?php _e('Enter your Product name', 'dc-woocommerce-multi-vendor'); ?>">
                            <input class="no_input" readonly type="text" name="product_manager[product_name]"
                            value="<?php echo ($product->get_title()) ? $product->get_title() : ''; ?>"
                            placeholder="<?php _e('Enter your Product name', 'dc-woocommerce-multi-vendor'); ?>">
                        </label>
                    </div>
                </div>
                <?php

                if ($product->is_type('variable')) {
                    ?>
                    <div class="list-row ">
                        <div class="row">
                            <div class="col-md-12">

                                <h4><?php _e('Product Variations', 'dc-woocommerce-multi-vendor'); ?>
                                    <input class="no_input" readonly type="hidden" name="product_manager[is_variable]"
                                    value="yes">
                                </h4>
                            </div>
                        </div>
                        <div class="row">
                            <?php

                            $available_variations = $product->get_available_variations();
                            foreach ($available_variations as $key => $value) {
                                $attr_title = array();
                                $attr_name = array();




                                ?>
                                <div class="col-md-6">
                                    <?php
                                    foreach ($value['attributes'] as $a_key => $val) {


                                        preg_match('/attribute_(.*)/', $a_key, $matches);
                                        $meta = get_post_meta($value['variation_id'], $a_key, true);
                                        $term = get_term_by('slug', $meta, $matches[1]);

                                        $tax = get_taxonomy($matches[1]);
                                        $attr_name[$matches[1]] = $meta;
                                        $attr_title[] = $tax->label . ":" . $term->name;
                                        ?>
                                        <input class="no_input" readonly type="hidden"
                                        name="product_manager[available_attributes][]"
                                        value="<?php echo $tax->name ?>">
                                        <?php

                                    }
                                    $variable_product1 = new WC_Product_Variation($value['variation_id']);

                                    ?>



                                    <label><?php _e(implode("|", $attr_title), 'dc-woocommerce-multi-vendor'); ?>

                                        <?php foreach ($attr_name as $v_key => $v_val): ?>
                                            <input class="no_input" readonly type="hidden"
                                            name="product_manager[variations][<?php echo $key ?>][attributes][<?php echo $v_key ?>]"
                                            value="<?php echo $v_val ?>">
                                        <?php endforeach; ?>
                                        <input class="no_input" readonly type="hidden"
                                        name="product_manager[variations][<?php echo $key ?>][variation_id]"
                                        value="<?php echo $value['variation_id'] ?>"
                                        placeholder="Enter your variation price">
                                        <input class="no_input" readonly type="text"
                                        name="product_manager[variations][<?php echo $key ?>][regular_price]"
                                        value="<?php echo $variable_product1->get_regular_price() ?>"
                                        placeholder="Enter your variation Regular price">
                                        <input class="no_input" readonly type="text"
                                        name="product_manager[variations][<?php echo $key ?>][sale_price]"
                                        value="<?php echo $variable_product1->get_sale_price() ?>"
                                        placeholder="Enter your variation Sale price">
                                    </label>

                                </div>
                                <?php

                            }

                            ?>
                        </div>
                    </div>

                    <?php
                } else {
                    ?>
                    <div class="row">
                        <div class="col-md-6">
                            <label><?php _e('Regular Price', 'dc-woocommerce-multi-vendor'); ?>

                                <input class="no_input" readonly type="text" name="product_manager[regular_price]"
                                value="<?php echo ($product->get_regular_price()) ? $product->get_regular_price() : ''; ?>"
                                placeholder="<?php _e('Enter your regular price', 'dc-woocommerce-multi-vendor'); ?>">
                            </label>
                        </div>
                        <div class="col-md-6">
                            <label><?php _e('Sale Price', 'dc-woocommerce-multi-vendor'); ?>

                                <input class="no_input" readonly type="text" name="product_manager[sale_price]"
                                value="<?php echo ($product->get_sale_price()) ? $product->get_sale_price() : ''; ?>"
                                placeholder="<?php _e('Enter your sale price', 'dc-woocommerce-multi-vendor'); ?>">
                            </label>
                        </div>
                    </div>
                    <?php
                }

                ?>


                <div class="row">
                    <div class="col-md-12">
                        <label>


                            <input class="no_input"  type="checkbox" name="product_manager[featured_product]"
                            value="1" <?php echo  ($product->get_featured())?"checked='checked'":'' ?>
                            > Featured Product
                        </label>
                    </div>
                </div>

                <div class="clearfix"></div>
                <?php do_action('km_woocommerce_product_data_panels', $product, true); ?>
                <p class="error_wcmp"><?php _e('* This field is required, you must fill some information.', 'dc-woocommerce-multi-vendor'); ?></p>
                <div class="action_div">
                    <button type="submit" class="wcmp_orange_btn"
                    name="store_save"><?php _e('Save Options', 'dc-woocommerce-multi-vendor'); ?></button>
                    <div class="clear"></div>
                </div>
            </div>


        </form>
    <?php elseif ($vendor): ?>
        <div class="wcmp_headding2"><?php _e('Product does not exist', 'dc-woocommerce-multi-vendor'); ?></div>
    <?php else: ?>
        <div class="wcmp_headding2"><?php _e('Product Detail', 'dc-woocommerce-multi-vendor'); ?></div>
        <div class="wcmp_headding2"><h3>Product is not exists</h3></div>

    <?php endif; ?>

<?php else: ?>

    <div class="wcmp_headding2"><?php _e('Product Detail', 'dc-woocommerce-multi-vendor'); ?></div>
    <div class="wcmp_headding2"><h3>Product is not relate with you. please owned it before edit it</h3></div>


<?php endif; ?>

