<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/13/2017
 * Time: 11:11 AM
 */

$_wcmp_enable_policy_tab = get_post_meta($product->get_id(), '_wcmp_enable_policy_tab', true) ? get_post_meta($product->get_id(), '_wcmp_enable_policy_tab', true) : '';
$_wcmp_cancallation_policy = get_post_meta($product->get_id(), '_wcmp_cancallation_policy', true) ? get_post_meta($product->get_id(), '_wcmp_cancallation_policy', true) : '';
$_wcmp_refund_policy = get_post_meta($product->get_id(), '_wcmp_refund_policy', true) ? get_post_meta($product->get_id(), '_wcmp_refund_policy', true) : '';
$_wcmp_shipping_policy = get_post_meta($product->get_id(), '_wcmp_shipping_policy', true) ? get_post_meta($product->get_id(), '_wcmp_shipping_policy', true) : '';
$settings_policies = get_option('wcmp_general_policies_settings_name');
$current_user_id = get_current_user_id();
?>

<?php
if (isset($settings_policies['is_cancellation_on'])) {
    if (isset($settings_policies['is_cancellation_product_level_on'])) {
        if ((is_user_wcmp_vendor($current_user_id) && (isset($settings_policies['can_vendor_edit_cancellation_policy']))) || current_user_can('manage_woocommerce')) {
            ?>

            <label><strong><?php echo __('Cancellation/Return/Exchange Policy'); ?> : </strong></label>
            <textarea <?php echo ($read_only) ? 'readonly' : '' ?> class="widefat no_input"
               name="_wcmp_cancallation_policy"><?php echo $_wcmp_cancallation_policy; ?></textarea>
               
               <?php
           }
       }
   }
   ?>
   <?php
   if (isset($settings_policies['is_refund_on'])) {
    if (isset($settings_policies['is_refund_product_level_on'])) {
        if ((is_user_wcmp_vendor($current_user_id) && (isset($settings_policies['can_vendor_edit_refund_policy']))) || current_user_can('manage_woocommerce')) {
            ?>
            
            <label><strong><?php echo __('Refund Policy'); ?> : </strong></label>
            <textarea <?php echo ($read_only) ? 'readonly' : '' ?> class="widefat no_input"
               name="_wcmp_refund_policy"><?php echo $_wcmp_refund_policy; ?></textarea>

               <?php
           }
       }
   }
   ?>
   <?php
   if (isset($settings_policies['is_shipping_on'])) {
    if (isset($settings_policies['is_shipping_product_level_on'])) {
        if ((is_user_wcmp_vendor($current_user_id) && (isset($settings_policies['can_vendor_edit_shipping_policy']))) || current_user_can('manage_woocommerce')) {
            ?>
            <label><strong><?php echo __('Shipping Policy'); ?> : </strong></label>
            <textarea <?php echo ($read_only) ? 'readonly' : '' ?> class="widefat no_input"
               name="_wcmp_shipping_policy"><?php echo $_wcmp_shipping_policy; ?></textarea>

               <?php
           }
       }
   }
   ?>



