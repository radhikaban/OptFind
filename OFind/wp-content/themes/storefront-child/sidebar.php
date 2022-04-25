<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/21/2017
 * Time: 10:41 AM
 */
?>
<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package storefront
 */

if (!is_active_sidebar('sidebar-1')) {
    return;
}
?>

<div id="secondary" class="widget-area" role="complementary">

    <?php if (is_woocommerce()): ?>
        <?php if (!function_exists('dynamic_sidebar') || !dynamic_sidebar("km-sidebar")) : ?>
            <?php dynamic_sidebar('Shop Sidebar'); ?>
        <?php endif; ?>

    <?php else: ?>
        <?php dynamic_sidebar('sidebar-1'); ?>
    <?php endif ?>

</div><!-- #secondary -->

