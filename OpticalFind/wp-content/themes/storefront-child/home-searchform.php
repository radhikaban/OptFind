<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/26/2017
 * Time: 11:09 AM
 */
?>
<?php
/**
 * The template for displaying product search form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/product-searchform.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */

if (!defined('ABSPATH')) {
    exit;
}

?>
<form role="search" method="post" class="woocommerce-product-search" action="<?php echo esc_url(home_url('/')); ?>">
    <?php if (isset($_REQUEST['kms']) && !empty($_REQUEST['kms'])) : ?>

        <span>Nothing found</span>
    <?php endif; ?>
    <label class="screen-reader-text"
           for="woocommerce-product-search-field-<?php echo isset($index) ? absint($index) : 0; ?>"><?php _e('Search for:', 'woocommerce'); ?></label>
    <input type="search" id="woocommerce-product-search-field-<?php echo isset($index) ? absint($index) : 0; ?>"
           class="search-field" placeholder="<?php echo esc_attr__('Search products&hellip;', 'woocommerce'); ?>"
           value="<?php echo isset($_REQUEST['kms']) ? $_REQUEST['kms'] : '' ?>" name="kms"/>
    <input type="submit" value="<?php echo esc_attr_x('Search', 'submit button', 'woocommerce'); ?>"/>

</form>

