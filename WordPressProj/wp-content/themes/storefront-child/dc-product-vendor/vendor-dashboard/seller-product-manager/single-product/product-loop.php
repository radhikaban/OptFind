<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/18/2017
 * Time: 11:05 AM
 */

global $woocommerce_loop;


?>

<?php while ($products->have_posts()) : $products->the_post(); ?>

    <?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }

    global $product;

    // Ensure visibility
    if (empty($product) || !$product->is_visible()) {
        return;
    }
    ?>
    <li <?php post_class(); ?>>
        <?php


        /**
         * woocommerce_before_shop_loop_item hook.
         *
         * @hooked woocommerce_template_loop_product_link_open - 10
         */
        do_action('woocommerce_before_shop_loop_item');

        /**
         * woocommerce_before_shop_loop_item_title hook.
         * @hooked woocommerce_template_loop_product_thumbnail - 10
         */


            do_action('km_woocommerce_before_shop_loop_item_title');


        /**
         * woocommerce_shop_loop_item_title hook.
         *
         * @hooked woocommerce_template_loop_product_title - 10
         */
        do_action('woocommerce_shop_loop_item_title');


        /**
         * woocommerce_after_shop_loop_item hook.
         *
         * @hooked woocommerce_template_loop_product_link_close - 5
         */

        if(isset($_POST['is_taxonomy']) ){
            do_action('km_woocommerce_after_shop_loop_item',$_POST);
        }else{
            do_action('km_woocommerce_after_shop_loop_item');

        }


        ?>
    </li>


<?php endwhile; // end of the loop. ?>
<li class="load_more_wrap" style="clear: both">
    <?php


    if ($paged < $page) {
        ?>
        <div id="more_posts" class="btn btn-primary">Load More</div>
        <input form="km-shop-archive-product"  type="hidden" id="km_pageenumber" name="pagenumber" value="<?php echo $paged + 1 ?>">
        <?php
    }
    ?>
</li>
