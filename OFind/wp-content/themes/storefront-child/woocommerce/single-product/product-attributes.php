<?php
/**
 * Product attributes
 *
 * Used by list_attributes() in the products class.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-attributes.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @author        WooThemes
 * @package    WooCommerce/Templates
 * @version     3.1.0
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="detail-table">
<table class="shop_attributes">
    <?php if ($display_dimensions && $product->has_weight()) : ?>
        <tr>
            <th><?php _e('Weight', 'woocommerce') ?></th>
            <td class="product_weight"><?php echo esc_html(wc_format_weight($product->get_weight())); ?></td>
        </tr>
    <?php endif; ?>

    <?php if ($display_dimensions && $product->has_dimensions()) : ?>
        <tr>
            <th><?php _e('Dimensions', 'woocommerce') ?></th>
            <td class="product_dimensions"><?php echo esc_html(wc_format_dimensions($product->get_dimensions(false))); ?></td>
        </tr>
    <?php endif; ?>
    <?php
    $brands = wp_get_post_terms(get_the_ID(), 'pwb-brand');

    if (!is_wp_error($brands)) {

        if (sizeof($brands > 0)) {

            $show_as = get_option('wc_pwb_admin_tab_brands_in_single');

            if ($show_as != 'no') {

                do_action('pwb_before_single_product_brands', $brands);

                echo '<tr>
    <th>Brands</th>
<td class="pwb-single-product-brands pwb-clearfix">';
                foreach ($brands as $brand) {
                    $brand_link = get_term_link($brand->term_id, 'pwb-brand');
                    $attachment_id = get_term_meta($brand->term_id, 'pwb_brand_image', 1);

                    $image_size = 'thumbnail';
                    $image_size_selected = get_option('wc_pwb_admin_tab_brand_logo_size');
                    if ($image_size_selected != false) {
                        $image_size = $image_size_selected;
                    }

                    $attachment_html = wp_get_attachment_image($attachment_id, $image_size);

                    if (!empty($attachment_html) && $show_as == 'brand_image' || !empty($attachment_html) && !$show_as) {
                        echo '<a href="' . $brand_link . '" title="' . __('View brand', 'perfect-woocommerce-brands') . '">' . $attachment_html . '</a>';
                    } else {
                        echo '<a href="' . $brand_link . '" title="' . __('View brand', 'perfect-woocommerce-brands') . '">' . $brand->name . '</a>';
                    }
                }
                echo '</td></tr>';

                do_action('pwb_after_single_product_brands', $brands);

            }

        }

    }
    ?>

    <?php foreach ($attributes as $attribute) : ?>
        <tr>
            <th><?php echo wc_attribute_label($attribute->get_name()); ?></th>
            <td><?php
                $values = array();

                if ($attribute->is_taxonomy()) {
                    $attribute_taxonomy = $attribute->get_taxonomy_object();
                    $attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'all'));

                    foreach ($attribute_values as $attribute_value) {
                        $value_name = esc_html($attribute_value->name);

                        if ($attribute_taxonomy->attribute_public) {
                            $values[] = '<a href="' . esc_url(get_term_link($attribute_value->term_id, $attribute->get_name())) . '" rel="tag">' . $value_name . '</a>';
                        } else {
                            $values[] = $value_name;
                        }
                    }
                } else {
                    $values = $attribute->get_options();

                    foreach ($values as &$value) {
                        $value = make_clickable(esc_html($value));
                    }
                }

                echo apply_filters('woocommerce_attribute', wpautop(wptexturize(implode(', ', $values))), $attribute, $values);
                ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</div>
<div class="taxonmomy-block">

    <?php foreach ($attributes as $attribute) : ?>
        <?php
        $values = array();
        $taxomoies_array = array('pa_colour', 'pa_gender');
        $current_attribute_color = '#FFFFFF';
        $current_attribute_image_src = WC_Swatches_Compatibility::wc_placeholder_img_src();
        $current_attribute_image_id = 0;
        $current_attribute_options = false;


        if ($attribute->is_taxonomy()) {
            $attribute_taxonomy = $attribute->get_taxonomy_object();


            $attribute_values = wc_get_product_terms($product->get_id(), $attribute->get_name(), array('fields' => 'all'));
            foreach ($attribute_values as $attribute_value) {

                if (!in_array($attribute_value->taxonomy, $taxomoies_array)):

                    $value_name = esc_html($attribute_value->name);
                    $current_size = 'swatches_image_size';
                    $swatch_term = new WC_Swatch_Term('swatches_id', $attribute_value->term_id, $attribute_value->taxonomy,false, $current_size);
                    $current_attribute_image_src = $swatch_term->thumbnail_src;

                    ?>
                    <div class="taxonmomy-item">
                        <img src="<?php echo $current_attribute_image_src ?>"><?php echo $value_name ?>
                    </div>
                <?php endif;

            }
        }


        ?>
    <?php endforeach; ?>

</div>