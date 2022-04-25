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
global $woocommerce, $WCMp;


$user_enable = get_user_meta(get_current_user_id(), '_vendor_turn_off', true);
 $paged = ( get_query_var( 'seller-product-manager' ) ) ? get_query_var( 'seller-product-manager' ) : 1;


$args = array(
    'post_type' => 'product',
    'posts_per_page' => KM_POST_PER_PAGE,
    'paged'          => $paged,
    'tax_query' => array(

        array(
            'taxonomy' => 'dc_vendor_shop',
            'field' => 'id',
            'terms' => get_user_meta(get_current_user_id(), '_vendor_term_id', true)
        )
    ));



$loop = new WP_Query($args);

?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>

    <div class="wcmp_tab">
        <div class="wcmp_tabbody" id="all">

            <form name="wcmp_vendor_dashboard_all_stat_export" method="post">
                <?php if ($loop->have_posts() && (empty($user_enable) || $user_enable != 'Enable')) { ?>

                    <div class="wcmp_table_loader"> <?php _e('Showing Results', 'dc-woocommerce-multi-vendor'); ?><span>



                    </div>
                <?php } ?>
                <?php
                @session_start();
                    if(isset($_SESSION['km_edit_products_date'])):
                        ?>
                        <div class="sucesss"><?php echo $_SESSION['km_edit_products_date'] ?>
                <?php
                    endif;
                    unset($_SESSION['km_edit_products_date']);
                ?>
                <div class="wcmp_table_holder">
                    <table width="100%" border="0" cellspacing="0" class="wcmp_order_all_table" cellpadding="0">
                        <tr>
                            <td class="no_display" align="center"
                                valign="top"><?php _e('Product Name', 'dc-woocommerce-multi-vendor'); ?><br>
                            </td>
                            <td class="no_display" align="center" valign="top"
                                width="20"><?php _e('Product Image', 'dc-woocommerce-multi-vendor'); ?></td>
                            <td align="center" class="no_display"
                                valign="top"> <?php _e('Product Price', 'dc-woocommerce-multi-vendor'); ?> </td>
                            <td align="center" class="no_display"
                                valign="top"> <?php _e('Product', 'dc-woocommerce-multi-vendor'); ?> </td>
                            <td class="no_display" align="center"
                                valign="top"><?php _e('Actions', 'dc-woocommerce-multi-vendor'); ?> </td>
                        </tr>
                        <?php if ($loop->have_posts() && (empty($user_enable) || $user_enable != 'Enable')) { ?>
                            <?php $WCMp->template->get_template('vendor-dashboard/seller-product-manager/seller-product-manager-item.php', array('vendor' => $vendor, 'loop' => $loop)); ?>


                        <?php } else { ?>
                            <tr>
                                <td colspan="5">
                                    <div class="wcmp_table_loader"> <?php _e('No Result Found', 'dc-woocommerce-multi-vendor'); ?></div>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>

            </form>
        </div>

    </div>
<?php
wp_reset_query();
