<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/7/2017
 * Time: 10:43 AM
 */

if (!defined('ABSPATH'))
    exit; // Exit if accessed directly


global $woocommerce, $WCMp;

if ($loop->have_posts()) :
    while ($loop->have_posts()) : $loop->the_post();
        global $product;

        ?>
        <tr>
            <td class="no_display" valign="middle" align="center"><a href="<?php echo get_permalink() ?>"
                                                                     target="_blank"><?php echo get_the_title() ?> </a>
            </td>
            <td class="no_display" align="center" valign="middle"><?php echo woocommerce_get_product_thumbnail() ?></td>
            <td class="no_display" valign="middle" align="center">
                <?php
                echo "&nbsp;" . $product->get_price_html();
                ?>
            </td>
            <td class="no_display" align="center" valign="middle"><?php echo ($product->get_status() == 'publish')?'published':$product->get_status(); ?></td>
            <td class="no_display" align="center" valign="middle">
                <?php
                $actions['view'] = array(
                    'url' => esc_url(wcmp_get_vendor_dashboard_endpoint_url(apply_filters('km_seller_add_product_manager_url', 'seller-add-product-manager'), get_the_ID())),
                    'img' => $WCMp->plugin_url . 'assets/images/view.png',
                    'title' => __('View', 'dc-woocommerce-multi-vendor'),
                );
                ?>
                <a title="View"
                   href="<?php echo esc_url(wcmp_get_vendor_dashboard_endpoint_url(apply_filters('km_seller_add_product_manager_url', 'seller-add-product-manager'), get_the_ID())); ?>"><i><img
                                src="<?php echo $WCMp->plugin_url . 'assets/images/view.png' ?>" alt=""></i></a>&nbsp;
                <script>
                    jQuery(document).ready(function ($) {
                        $('.remove-product-<?php echo $product->get_slug() ?>').confirm({
                            title: 'Confirmation Dialog',
                            content: 'Are you sure you want remove this product on your shop list!' +
                            '<form action="" method="post" class="formName">' +
                            '<div class="form-group"><?php echo wp_nonce_field('km-delete-product', 'km_delete_product') ?>' +
                            '<input type="hidden" class="name form-control" name="product-slug" required value="<?php echo $product->get_slug() ?>" />' +
                            '</div>' +
                            '</form>',
                            buttons: {
                                formSubmit: {
                                    text:
                                        'Confirm',
                                    btnClass: 'btn-blue',
                                    action: function () {
                                        this.$content.find('form').submit()
                                    },
                                },
                                cancel:
                                    function () {
                                    }
                            }
                        });
                    })
                </script>
                <button class="remove-product-<?php echo $product->get_slug() ?> wcmp_ass_btn" data -
                        slug="<?php echo $product->get_slug() ?>"> Delete
                </button>

            </td>
        </tr>
        <?php
    endwhile;
    ?>
    <tr>
        <td colspan="5">
            <div class="pagination">
                <?php
                echo paginate_links(array(
                    'base' => change_product_page_url() . "%#%/",
                    'total' => $loop->max_num_pages,
                    'current' => max(1, get_query_var('seller-product-manager')),
                    'format' => '%#%',
                    'show_all' => false,
                    'type' => 'plain',
                    'end_size' => 2,
                    'mid_size' => 1,
                    'prev_next' => true,
                    'prev_text' => sprintf('<i></i> %1$s', __('Previous', 'text-domain')),
                    'next_text' => sprintf('%1$s <i></i>', __('Next', 'text-domain')),
                    'add_args' => false,
                    'add_fragment' => '',
                ));
                ?>

            </div>
        </td>
    </tr>
    <?php
endif;