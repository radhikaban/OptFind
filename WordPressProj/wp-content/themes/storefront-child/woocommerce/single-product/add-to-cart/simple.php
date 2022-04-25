<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

echo wc_get_stock_html( $product );

if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form class="cart" action="<?php echo esc_url( get_permalink() ); ?>" method="post" enctype='multipart/form-data'>
		<?php
			/**
			 * @since 2.1.0.
			 */
			do_action( 'woocommerce_before_add_to_cart_button' );

			/**
			 * @since 3.0.0.
			 */
			do_action( 'woocommerce_before_add_to_cart_quantity' );

			woocommerce_quantity_input( array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( $_POST['quantity'] ) : $product->get_min_purchase_quantity(),
			) );

			/**
			 * @since 3.0.0.
			 */
			do_action( 'woocommerce_after_add_to_cart_quantity' );
		?>



        <?php
        $val = km_add_to_cart_button();
        if($val):
            echo '<a  href="#" class="add_to_cart_more_offer btn btn-default">Add To Cart</a>';
        ?>
        <div id="dialog" title="Where You Want To Pickup Your Order">
        <?php
        km_more_vendor_by_distance_simple();
        ?>
        </div>
            <?php
        else:

        ?>
		<!-- <button type="submit" name="add-to-cart" value="<?php // echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt"><?php // echo esc_html( $product->single_add_to_cart_text() ); ?></button> -->
        <a rel="nofollow" href="?add-to-cart=<?php echo esc_attr( $product->get_id() ); ?>"  data-product_id="<?php echo esc_attr( $product->get_id() ); ?>" data-product_sku="" class="button product_type_simple add_to_cart_button btn-default">Add to cart</a>

		<?php

        endif;
			/**
			 * @since 2.1.0.
			 */
			do_action( 'woocommerce_after_add_to_cart_button' );
		?>
	</form>
    <script>
        jQuery(document).ready(function () {
            jQuery(document).on('click', ".add_to_cart_more_offer", function (e) {
                e.preventDefault();
                jQuery("#dialog").dialog("open");
            })
        })
    </script>
    <script>
        jQuery(function ($) {
            $("#dialog").dialog({
                autoOpen: false,
                show: {
                    effect: "blind",
                    duration: 1000
                },
                hide: {
                    effect: "explode",
                    duration: 1000
                },
                resizable:false,
                draggable:false,
                modal:true
            });

        });

    </script>


	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
