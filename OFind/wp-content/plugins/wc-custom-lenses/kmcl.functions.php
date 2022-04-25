<?php
/**
 * Created by PhpStorm.
 * User: Sarab
 * Date: 15-Dec-17
 * Time: 10:50 AM
 */

function kmcl_template_path()
{
    return apply_filters('kmcl_template_path', 'wc-custom-lenses/');
}

function kmcl_plugin_path()
{
    return untrailingslashit(plugin_dir_path(KMCL_PLUGIN_FILE));
}


if (!function_exists('kmcl_single_variation')) {

    /**
     * Output placeholders for the single variation.
     */
    function kmcl_single_variation()
    {
        echo '<div class="woocommerce-variation single_variation"></div>';
    }
}

if (!function_exists('kmcl_single_variation_add_to_cart_button')) {

    /**
     * Output the add to cart button for variations.
     */
    function kmcl_single_variation_add_to_cart_button()
    {
        kmcl_get_template('single-product/add-to-cart/variation-add-to-cart-button.php');
    }
}

if (!function_exists('kmcl_simple_add_to_cart')) {

    /**
     * Output the simple product add to cart area.
     *
     * @subpackage    Product
     */
    function kmcl_simple_add_to_cart()
    {
        kmcl_get_template('single-product/add-to-cart/simple.php');
    }
}


if (!function_exists('kmcl_simple_add_to_cart')) {

    /**
     * Output the simple product add to cart area.
     *
     * @subpackage    Product
     */
    function kmcl_simple_add_to_cart()
    {
        kmcl_get_template('single-product/add-to-cart/simple.php');
    }
}


if (!function_exists('kmcl_grouped_add_to_cart')) {

    /**
     * Output the grouped product add to cart area.
     *
     * @subpackage    Product
     */
    function kmcl_grouped_add_to_cart()
    {
        global $product;

        $products = array_filter(array_map('wc_get_product', $product->get_children()), 'wc_products_array_filter_visible_grouped');

        if ($products) {
            kmcl_get_template('single-product/add-to-cart/grouped.php', array(
                'grouped_product' => $product,
                'grouped_products' => $products,
                'quantites_required' => false,
            ));
        }
    }
}

if (!function_exists('kmcl_variable_add_to_cart')) {

    /**
     * Output the variable product add to cart area.
     *
     * @subpackage    Product
     */
    function kmcl_variable_add_to_cart()
    {
        global $product;
        // Enqueue variation scripts.
        wp_enqueue_script('wc-add-to-cart-variation');

        // Get Available variations?
        $get_variations = count($product->get_children()) <= apply_filters('woocommerce_ajax_variation_threshold', 30, $product);

        // Load the template.
        kmcl_get_template('single-product/add-to-cart/variable.php', array(
            'available_variations' => $get_variations ? $product->get_available_variations() : false,
            'attributes' => $product->get_variation_attributes(),
            'selected_attributes' => $product->get_default_attributes(),
        ));
    }
}

if (!function_exists('kmcl_external_add_to_cart')) {

    /**
     * Output the external product add to cart area.
     *
     * @subpackage    Product
     */
    function kmcl_external_add_to_cart()
    {
        global $product;

        if (!$product->add_to_cart_url()) {
            return;
        }

        kmcl_get_template('single-product/add-to-cart/external.php', array(
            'product_url' => $product->add_to_cart_url(),
            'button_text' => $product->single_add_to_cart_text(),
        ));
    }
}

function kmcl_get_template($template_name, $args = array(), $template_path = '', $default_path = '')
{
    if (!empty($args) && is_array($args)) {
        extract($args);
    }

    $located = kmcl_locate_template($template_name, $template_path, $default_path);


    if (!file_exists($located)) {
        wc_doing_it_wrong(__FUNCTION__, sprintf(__('%s does not exist.', 'wc-custom-lenses'), '<code>' . $located . '</code>'), '2.1');
        return;
    }

    // Allow 3rd party plugin filter template file from their plugin.
    $located = apply_filters('kmcl_get_template', $located, $template_name, $args, $template_path, $default_path);

    do_action('woocommerce_before_template_part', $template_name, $template_path, $located, $args);

    include_once($located);

    do_action('woocommerce_after_template_part', $template_name, $template_path, $located, $args);
}

function kmcl_locate_template($template_name, $template_path = '', $default_path = '')
{

    if (!$template_path) {
        $template_path = kmcl_template_path();
    }


    if (!$default_path) {
        $default_path = kmcl_plugin_path() . '/templates/';
    }


    // Look within passed path within the theme - this is priority.

    $template = locate_template(
        array(
            trailingslashit($template_path) . $template_name,
            $template_name,
        )
    );

    if (!$template) {
        $template = $default_path . $template_name;
    }

    // Return what we found.
    return apply_filters('kmcl_locate_template', $template, $template_name, $template_path);
}


add_filter('woocommerce_add_cart_item_data', 'km_add_item_data', 1, 3);
function km_add_item_data($cart_item_data, $product_id, $variation_id)
{

    global $woocommerce;
    @session_start();
    $new_value = array();

    if (isset($cart_item_data['post'])) {
        $post = get_post($cart_item_data['post']);

        if ($post) {
            $new_value['lens_title'] = $post->post_title;

            $or_price = 0;
            $regular_price = get_post_meta($post->ID, '_regular_price', true);

            $sale_price = get_post_meta($post->ID, '_sale_price', true);
            if (isset($regular_price) && !empty($regular_price)) {
                if (isset($sale_price) && !empty($sale_price) && $sale_price < $regular_price):

                    $or_price = $sale_price;
                else:
                    $or_price = $regular_price;
                endif;
            }

            $new_value['price'] = $or_price;
            $li = array();
            $lt = array();
            $lc = array();
            $lensindex = wp_get_post_terms($post->ID, 'lenses-index');
            foreach ($lensindex as $lindex) {
                $li[] = $lindex->name;
            }
            $lenstypes = wp_get_post_terms($post->ID, 'lenses-types');
            foreach ($lenstypes as $lenstype) {
                $lt[] = $lenstype->name;
            }
            $lenscategory = wp_get_post_terms($post->ID, 'lenses-category');
            foreach ($lenscategory as $lensc) {
                $lc[] = $lensc->name;
            }
            $new_value['lens_index'] = implode(", ", $li);
            $new_value['lens_category'] = implode(", ", $lc);
            $new_value['lens_type'] = implode(", ", $lt);

        }

    }
    if (isset($cart_item_data['skip_step']) && $cart_item_data['skip_step'] == 'false') {

        if(isset($cart_item_data['sph-right'])){
            $new_value['sph-right'] = (isset($cart_item_data['sph-right']) && !empty($cart_item_data['sph-right'])) ? $cart_item_data['sph-right'] : "0.00";
        }
        if(isset($cart_item_data['sph-left'])){
            $new_value['sph-left'] = (isset($cart_item_data['sph-left']) && !empty($cart_item_data['sph-left'])) ? $cart_item_data['sph-left'] : "0.00";
        }
        if(isset($cart_item_data['cyl-right'])){
           $new_value['cyl-right'] = (isset($cart_item_data['cyl-right']) && !empty($cart_item_data['cyl-right'])) ? $cart_item_data['cyl-right'] : "0.00";
        }
        if(isset($cart_item_data['cyl-left'])){
            $new_value['cyl-left'] = (isset($cart_item_data['cyl-left']) && !empty($cart_item_data['cyl-left'])) ? $cart_item_data['cyl-left'] : "0.00";
        }
        if(isset($cart_item_data['axis-left'])){
            $new_value['axis-left'] = (isset($cart_item_data['axis-left']) && !empty($cart_item_data['axis-left'])) ? $cart_item_data['axis-left'] : "0.00";
        }
        if(isset($cart_item_data['axis-right'])){
            $new_value['axis-right'] = (isset($cart_item_data['axis-right']) && !empty($cart_item_data['axis-right'])) ? $cart_item_data['axis-right'] : "0.00";
        }
        if(isset($cart_item_data['add-left'])){
           $new_value['add-left'] = (isset($cart_item_data['add-left']) && !empty($cart_item_data['add-left'])) ? $cart_item_data['add-left'] : "0.00";
        }
        if(isset($cart_item_data['add-right'])){
            $new_value['add-right'] = (isset($cart_item_data['add-right']) && !empty($cart_item_data['add-right'])) ? $cart_item_data['add-right'] : "0.00";
        }
        

        if(isset($cart_item_data['power-right'])){
            $new_value['power-right'] = $cart_item_data['power-right'];
        }
         if(isset($cart_item_data['bc-right'])){
            $new_value['bc-right'] = $cart_item_data['bc-right'];
        }
         if(isset($cart_item_data['boxes-right'])){
            $new_value['boxes-right'] = $cart_item_data['boxes-right'];
        }
         if(isset($cart_item_data['power-left'])){
            $new_value['power-left'] = $cart_item_data['power-left'];
        }
         if(isset($cart_item_data['bc-left'])){
            $new_value['bc-left'] = $cart_item_data['bc-left'];
        }
         if(isset($cart_item_data['boxes-left'])){
            $new_value['boxes-left'] = $cart_item_data['boxes-left'];
        }

        if ((isset($cart_item_data['right-pd']) && !empty($cart_item_data['right-pd'])) || isset($cart_item_data['left-pd']) && !empty($cart_item_data['left-pd'])) {
            $new_value['right-pd'] = (isset($cart_item_data['right-pd']) && !empty($cart_item_data['right-pd'])) ? $cart_item_data['right-pd'] : "0";

            $new_value['left-pd'] = (isset($cart_item_data['left-pd']) && !empty($cart_item_data['left-pd'])) ? $cart_item_data['left-pd'] : "0";

        } else {
            $new_value['pd'] = (isset($cart_item_data['pd']) && !empty($cart_item_data['pd'])) ? $cart_item_data['pd'] : "0.00";

        }


        $new_value['skip_step'] = (isset($cart_item_data['skip_step']) && !empty($cart_item_data['skip_step'])) ? $cart_item_data['skip_step'] : "true";
    }


    if (isset($cart_item_data['prescription_url']) && !empty($cart_item_data['prescription_url'])) {
        $new_value['prescription'] = $cart_item_data['prescription_url'];
    }

    if (empty($cart_item_data)) {
        return $new_value;
    } else {
        return array_merge($cart_item_data, $new_value);
    }
}


add_filter('woocommerce_cart_item_name', 'km_add_user_custom_data_from_session_into_cart', 1, 3);

if (!function_exists('km_add_user_custom_data_from_session_into_cart')) {
    function km_add_user_custom_data_from_session_into_cart($product_name, $values, $cart_item_key)
    {
        $return_var_start = $return_var_end = $string = '';

        $return_string = $product_name;
        $return_var_start = "<dl class='variation'><table class='wdm_options_table' id='" . $values['variation_id'] . "'>";
        $return_var_end = "</table></dl>";
        $string .= "<tr><td><strong>" . $return_string . "</strong></td></tr>";
        $return = false;
        if (isset($values['lens_title']) && !empty($values['lens_title'])) {
            $return = true;
            $string .= "<tr><td><strong>Lens Title :- </strong>" . ($values['lens_title']) . "</td></tr>";
        }
        if (isset($values['price']) && !empty($values['price'])) {

            $return = true;
            $string .= "<tr><td><strong>Lens Price :- </strong>$" . $values['price'] . "</td></tr>";
        }

        if (isset($values['lens_index']) && !empty($values['lens_index'])) {
            $return = true;
            $string .= "<tr><td><strong>Dioptre Range :- </strong>" . $values['lens_index'] . "</td></tr>";
        }
        if (isset($values['lens_category']) && !empty($values['lens_category'])) {
            $return = true;
            $string .= "<tr><td><strong>Lens Category :- </strong>" . $values['lens_category'] . "</td></tr>";
        }
        if (isset($values['lens_type']) && !empty($values['lens_type'])) {
            $return = true;
            $string .= "<tr><td><strong> Lens Types:- </strong>" . $values['lens_type'] . "</td></tr>";
        }


        if (isset($values['skip_step']) && $values['skip_step'] == 'false') {

            if (isset($values['prescription']) && !empty($values['prescription'])) {
                $return = true;
                $string .= "<tr><td><strong> PRESCRIPTION:- </strong> <a download  href='" . $values['prescription'] . "'>" . basename($values['prescription']) . "</a></td></tr>";
            }

            if (isset($values['sph-right']) && !empty($values['sph-right'])) {
                $return = true;
                $string .= "<tr><td><strong> SPH RIGHT:- </strong>" . $values['sph-right'] . "</td></tr>";
            }
            if (isset($values['sph-left']) && !empty($values['sph-left'])) {
                $return = true;
                $string .= "<tr><td><strong> SPH LEFT:- </strong>" . $values['sph-left'] . "</td></tr>";
            }

            if (isset($values['cyl-right']) && !empty($values['cyl-right'])) {
                $return = true;
                $string .= "<tr><td><strong> CYL RIGHT:- </strong>" . $values['cyl-right'] . "</td></tr>";
            }
            if (isset($values['cyl-left']) && !empty($values['cyl-left'])) {
                $return = true;
                $string .= "<tr><td><strong> CYL LEFT:- </strong>" . $values['cyl-left'] . "</td></tr>";
            }
            if (isset($values['axis-right']) && !empty($values['axis-right'])) {
                $return = true;
                $string .= "<tr><td><strong> AXIS RIGHT:- </strong>" . $values['axis-right'] . "</td></tr>";
            }
            if (isset($values['axis-left']) && !empty($values['axis-left'])) {
                $return = true;
                $string .= "<tr><td><strong> AXIS LEFT:- </strong>" . $values['axis-left'] . "</td></tr>";
            }
            if (isset($values['add-right']) && !empty($values['add-right'])) {
                $return = true;
                $string .= "<tr><td><strong> ADD RIGHT:- </strong>" . $values['add-right'] . "</td></tr>";
            }
            if (isset($values['add-left']) && !empty($values['add-left'])) {
                $return = true;
                $string .= "<tr><td><strong> ADD LEFT:- </strong>" . $values['add-left'] . "</td></tr>";
            }

            if (isset($values['power-right']) && !empty($values['power-right'])) {
                $return = true;
                $string .= "<tr><td><strong> POWER RIGHT:- </strong>" . $values['power-right'] . "</td></tr>";
            }
            if (isset($values['power-left']) && !empty($values['power-left'])) {
                $return = true;
                $string .= "<tr><td><strong> POWER LEFT:- </strong>" . $values['power-left'] . "</td></tr>";
            }
            if (isset($values['bc-right']) && !empty($values['bc-right'])) {
                $return = true;
                $string .= "<tr><td><strong> BC RIGHT:- </strong>" . $values['bc-right'] . "</td></tr>";
            }
            if (isset($values['bc-left']) && !empty($values['bc-left'])) {
                $return = true;
                $string .= "<tr><td><strong> BC LEFT:- </strong>" . $values['bc-left'] . "</td></tr>";
            }
            if (isset($values['boxes-right']) && !empty($values['boxes-right'])) {
                $return = true;
                $string .= "<tr><td><strong> BOXES RIGHT:- </strong>" . $values['boxes-right'] . "</td></tr>";
            }
            if (isset($values['boxes-left']) && !empty($values['boxes-left'])) {
                $return = true;
                $string .= "<tr><td><strong> BOXES LEFT:- </strong>" . $values['boxes-left'] . "</td></tr>";
            }


            if ((isset($values['right-pd']) && !empty($values['right-pd'])) || (isset($values['left-pd']) && !empty($values['left-pd']))) {
                $return = true;
                if (isset($values['right-pd']) && !empty($values['right-pd'])) {
                    $string .= "<tr><td><strong> RIGHT PD:- </strong>" . $values['right-pd'] . "</td></tr>";

                }
                if (isset($values['left-pd']) && !empty($values['left-pd'])) {
                    $string .= "<tr><td><strong> LEFT PD:- </strong>" . $values['left-pd'] . "</td></tr>";

                }

            } else if (isset($values['pd']) && !empty($values['pd'])) {
                $return = true;
                $string .= "<tr><td><strong>PD:- </strong>" . $values['pd'] . "</td></tr>";

            }
        }


        if ($return) {
            return $return_var_start . $string . $return_var_end;
        }
        return $return_string;

    }
}


add_action('woocommerce_before_calculate_totals', 'add_custom_price');
function add_custom_price($cart_object)
{
    foreach ($cart_object->cart_contents as $key => $cart_item) {
        if (WC()->session->get($key . "price")) {
            $product = wc_get_product($cart_item['data']->get_id());
            $price = $product->get_price() + WC()->session->get($key . 'price');

            $cart_item['data']->set_price($price);
        }
    }
}

add_filter('woocommerce_checkout_cart_item_quantity', 'remove_qty');
function remove_qty()
{
    return '';
}

add_action('woocommerce_add_order_item_meta', 'km_add_custom_data_to_order_item_meta', 1, 2);
if (!function_exists('km_add_custom_data_to_order_item_meta')) {
    function km_add_custom_data_to_order_item_meta($item_id, $values)
    {
        global $woocommerce, $wpdb;

        if (!empty($values['lens_title'])) {
            wc_add_order_item_meta($item_id, 'lens_title', $values['lens_title']);
        }

        if (!empty($values['price'])) {
            wc_add_order_item_meta($item_id, 'price', $values['price']);
        }

        if (!empty($values['lens_index'])) {
            wc_add_order_item_meta($item_id, 'lens_index', $values['lens_index']);
        }

        if (!empty($values['lens_category'])) {
            wc_add_order_item_meta($item_id, 'lens_category', $values['lens_category']);
        }
        if (!empty($values['lens_type'])) {
            wc_add_order_item_meta($item_id, 'lens_type', $values['lens_type']);
        }


        if (isset($values['skip_step']) && $values['skip_step'] == 'false') {

            if (isset($values['prescription']) && !empty($values['prescription'])) {
                wc_add_order_item_meta($item_id, 'prescription', $values['prescription']);
            }

            if (isset($values['sph-right']) && !empty($values['sph-right'])) {
                wc_add_order_item_meta($item_id, 'sph-right', $values['sph-right']);
            }
            if (isset($values['sph-left']) && !empty($values['sph-left'])) {
                wc_add_order_item_meta($item_id, 'sph-left', $values['sph-left']);
            }

            if (isset($values['cyl-right']) && !empty($values['cyl-right'])) {
                wc_add_order_item_meta($item_id, 'cyl-right', $values['cyl-right']);
            }
            if (isset($values['cyl-left']) && !empty($values['cyl-left'])) {
                wc_add_order_item_meta($item_id, 'cyl-left', $values['cyl-left']);
            }

            if (isset($values['axis-right']) && !empty($values['axis-right'])) {
                wc_add_order_item_meta($item_id, 'axis-right', $values['axis-right']);
            }
            if (isset($values['axis-left']) && !empty($values['axis-left'])) {
                wc_add_order_item_meta($item_id, 'axis-left', $values['axis-left']);
            }

            if (isset($values['add-right']) && !empty($values['add-right'])) {
                wc_add_order_item_meta($item_id, 'add-right', $values['add-right']);
            }
            if (isset($values['add-left']) && !empty($values['add-left'])) {
                wc_add_order_item_meta($item_id, 'add-left', $values['add-left']);
            }

            if (isset($values['power-right']) && !empty($values['power-right'])) {
                wc_add_order_item_meta($item_id, 'power-right', $values['power-right']);
            }
            if (isset($values['bc-right']) && !empty($values['bc-right'])) {
                wc_add_order_item_meta($item_id, 'bc-right', $values['bc-right']);
            }
            if (isset($values['boxes-right']) && !empty($values['boxes-right'])) {
                wc_add_order_item_meta($item_id, 'boxes-right', $values['boxes-right']);
            }
            if (isset($values['power-left']) && !empty($values['power-left'])) {
                wc_add_order_item_meta($item_id, 'power-left', $values['power-left']);
            }
            if (isset($values['bc-left']) && !empty($values['bc-left'])) {
                wc_add_order_item_meta($item_id, 'bc-left', $values['bc-left']);
            }
            if (isset($values['boxes-left']) && !empty($values['boxes-left'])) {
                wc_add_order_item_meta($item_id, 'boxes-left', $values['boxes-left']);
            }


            if ((isset($values['right-pd']) && !empty($values['right-pd'])) || (isset($values['left-pd']) && !empty($values['left-pd']))) {

                if (isset($values['right-pd']) && !empty($values['right-pd'])) {
                    wc_add_order_item_meta($item_id, 'right-pd', $values['right-pd']);
                }
                if (isset($values['left-pd']) && !empty($values['left-pd'])) {
                    wc_add_order_item_meta($item_id, 'left-pd', $values['left-pd']);
                }

            } else if (isset($values['pd']) && !empty($values['pd'])) {
                if (isset($values['pd']) && !empty($values['pd'])) {
                    wc_add_order_item_meta($item_id, 'pd', $values['pd']);
                }
            }
        }
    }
}

add_action('woocommerce_before_cart_item_quantity_zero', 'km_remove_user_custom_data_from_cart', 1, 1);
if (!function_exists('km_remove_user_custom_data_from_cart')) {
    function km_remove_user_custom_data_from_cart($cart_item_key)
    {
        global $woocommerce;

        $cart = $woocommerce->cart->get_cart();

        foreach ($cart as $key => $values) {


            WC()->session->__unset($key);
            if ($values['lens_title'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }

            if ($values['price'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }

            if ($values['lens_index'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['lens_category'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['lens_type'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['prescription'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['sph-right'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['sph-left'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }

            if ($values['power-right'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['bc-right'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['boxes-right'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['power-left'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['bc-left'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['boxes-left'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }


            if ($values['cyl-right'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['cyl-left'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['add-left'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['add-right'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['axis-right'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['axis-left'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['right-pd'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['left-pd'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }
            if ($values['pd'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }

            if ($values['skip_step'] == $cart_item_key) {
                unset($woocommerce->cart->cart_contents[$key]);
            }

        }
    }
}


function km_add_to_cart_button()
{
    global $WCMp, $post, $wpdb;
    $results_str = '';
    $searchstr = $post->post_title;

    if (isset($_REQUEST['vendor']) && !empty($_REQUEST['vendor']) && wc_get_product($_REQUEST['vendor']) && get_post_status($_REQUEST['vendor']) == 'publish') {
        $p_parent_id = get_post_meta($post->ID, 'p_parent_id', true);
        $querystr = "select * from " . KM_MAP_TABLE . " where product_id = '{$p_parent_id}'";
    } else {
        $querystr = "select * from " . KM_MAP_TABLE . " where product_id = '{$post->ID}'";
    }


    $results_obj_arr = $wpdb->get_results($querystr);


    if (isset($results_obj_arr) && count($results_obj_arr) > 0) {
        $results_str = $results_obj_arr[0]->product_ids;
    }

    if (!empty($results_str)) {
        $product_id_arr = explode(',', $results_str);

        if (isset($_GET['vendor'])) {
            if (($key = array_search($_GET['vendor'], $product_id_arr)) !== false) {
                unset($product_id_arr[$key]);
            }
        }
        if (count($product_id_arr) > 0) {
            $args = array(
                'posts_per_page' => -1,
                'offset' => 0,
                'orderby' => 'date',
                'order' => 'DESC',
                'post_type' => 'product',
                'post__in' => $product_id_arr,
                'post_status' => 'publish',
                'suppress_filters' => true
            );
            $results = get_posts($args);


            if (count($results) > 0) {
                return true;
                exit;
            }
        }

    }
    return;
}


function km_more_vendor_by_distance_simple()
{

    @session_start();
    $lat = ($_SESSION['lat']) ? $_SESSION['lat'] : '';
    $lang = ($_SESSION['lang']) ? $_SESSION['lang'] : '';


    global $WCMp, $post, $wpdb;
    $results_str = '';

    if (isset($_REQUEST['vendor']) && !empty($_REQUEST['vendor']) && wc_get_product($_REQUEST['vendor']) && get_post_status($_REQUEST['vendor']) == 'publish') {
        $p_parent_id = get_post_meta($post->ID, 'p_parent_id', true);
        $querystr = "select * from " . KM_MAP_TABLE . " where product_id = '{$p_parent_id}'";
    } else {
        $querystr = "select * from " . KM_MAP_TABLE . " where product_id = '{$post->ID}'";
    }


    $results_obj_arr = $wpdb->get_results($querystr);


    if (isset($results_obj_arr) && count($results_obj_arr) > 0) {
        $results_str = $results_obj_arr[0]->product_ids;
    }
    $areaDistance = array();
    if (!empty($results_str)) {
        $product_id_arr = explode(',', $results_str);

        if (!empty($lat) && !empty($lang)) {
            echo $dis_loc = "SELECT DISTINCT object_id ,  6371 * 2 * ASIN(SQRT(
                POWER(SIN((l.lat - abs('{$lat}')) * pi()/180 / 2),
                2) + COS(l.lat * pi()/180 ) * COS(abs('{$lat}') *
                pi()/180) * POWER(SIN((l.lang - '{$lang}') *
                pi()/180 / 2), 2) )) as distance FROM {$wpdb->term_relationships} as tr
              inner join {$wpdb->termmeta} as tm on tr.term_taxonomy_id = tm.term_id and tm.meta_key = '_vendor_user_id'
              left join " . KM_LATLANG_TABLE . " as l on tm.meta_value = l.vendor_id and tm.meta_key = '_vendor_user_id'
               where object_id in ({$results_str}) ORDER BY  6371 * 2 * ASIN(SQRT(
                POWER(SIN((l.lat - abs('{$lat}')) * pi()/180 / 2),
                2) + COS(l.lat * pi()/180 ) * COS(abs('{$lat}') *
                pi()/180) * POWER(SIN((l.lang - '{$lang}') *
                pi()/180 / 2), 2) ))";


            $dis_results = $wpdb->get_results($dis_loc);


            if ($dis_results) {
                $product_id_arr = array();
                foreach ($dis_results as $dis_result):
                    $product_id_arr[] = $dis_result->object_id;
                    $areaDistance[$dis_result->object_id] = $dis_result->distance;
                endforeach;
            }
        }


        if (count($product_id_arr) > 0) {
            $args = array(
                'posts_per_page' => -1,
                'offset' => 0,
                'orderby' => 'post__in',
                'order' => 'DESC',
                'post_type' => 'product',
                'post__in' => $product_id_arr,
                'post_status' => 'publish',
                'suppress_filters' => true
            );
            $results = get_posts($args);

            $i = 0;
            $more_product_array = array();
            foreach ($results as $result) {
                $vendor_data = get_wcmp_product_vendors($result->ID);
                $_product = wc_get_product($result->ID);
                $other_user = new WP_User($result->post_author);
                if ($_product->is_visible() && !is_user_wcmp_pending_vendor($other_user) && !is_user_wcmp_rejected_vendor($other_user)) {
                    if ($vendor_data) {
                        if (isset($vendor_data->user_data->data->display_name)) {
                            $more_product_array[$i]['seller_name'] = $vendor_data->user_data->data->display_name;
                            $more_product_array[$i]['is_vendor'] = 1;
                            $terms = get_the_terms($result, 'dc_vendor_shop');
                            if (!empty($terms)) {
                                $more_product_array[$i]['shop_link'] = get_term_link($terms[0], 'dc_vendor_shop');
                                $more_product_array[$i]['rating_data'] = wcmp_get_vendor_review_info($terms[0]->term_id);
                            }
                        }
                    } else {
                        $more_product_array[$i]['seller_name'] = $other_user->data->display_name;
                        $more_product_array[$i]['is_vendor'] = 0;
                        $more_product_array[$i]['shop_link'] = get_permalink(wc_get_page_id('shop'));
                        $more_product_array[$i]['rating_data'] = 'admin';
                    }
                    $currency_symbol = get_woocommerce_currency_symbol();
                    $regular_price_val = $_product->get_regular_price();
                    $sale_price_val = $_product->get_sale_price();
                    $price_val = $_product->get_price();
                    $more_product_array[$i]['product_name'] = $result->post_title;
                    $more_product_array[$i]['regular_price_val'] = $regular_price_val;
                    $more_product_array[$i]['sale_price_val'] = $sale_price_val;
                    $more_product_array[$i]['price_val'] = $price_val;
                    $more_product_array[$i]['product_id'] = $result->ID;
                    $more_product_array[$i]['product_type'] = $_product->get_type();
                    if ($_product->get_type() == 'variable') {
                        $more_product_array[$i]['_min_variation_price'] = get_post_meta($result->ID, '_min_variation_price', true);
                        $more_product_array[$i]['_max_variation_price'] = get_post_meta($result->ID, '_max_variation_price', true);
                        $variable_min_sale_price = get_post_meta($result->ID, '_min_variation_sale_price', true);
                        $variable_max_sale_price = get_post_meta($result->ID, '_max_variation_sale_price', true);
                        $more_product_array[$i]['_min_variation_sale_price'] = $variable_min_sale_price ? $variable_min_sale_price : $more_product_array[$i]['_min_variation_price'];
                        $more_product_array[$i]['_max_variation_sale_price'] = $variable_max_sale_price ? $variable_max_sale_price : $more_product_array[$i]['_max_variation_price'];
                        $more_product_array[$i]['_min_variation_regular_price'] = get_post_meta($result->ID, '_min_variation_regular_price', true);
                        $more_product_array[$i]['_max_variation_regular_price'] = get_post_meta($result->ID, '_max_variation_regular_price', true);
                    }
                    $i++;
                }
            }

            if (count($more_product_array) > 0):
                ?>
                <div>
                    <div class="name">Name</div>
                    <div class="name">Address</div>
                    <div class="name">Price</div>
                    <?php if (count($areaDistance) > 0) { ?>
                        <div class="name">Distance</div>
                    <?php } ?>
                    <div class="name">Action</div>
                </div>

            <?php

            endif;

            foreach ($more_product_array as $more_product) {
                $_product = wc_get_product($more_product['product_id']);
                ?>
                <div class="name">
                    <a href="<?php echo $more_product['shop_link']; ?>"
                       class="wcmp_seller_name"><?php echo $more_product['seller_name']; ?></a>
                    <?php do_action('after_wcmp_singleproductmultivendor_vendor_name', $more_product['product_id'], $more_product); ?>
                </div>
                <div class="address">



                    <?php
                    $vendor = get_wcmp_product_vendors($_product->get_id());
                    $vendor_address = array();
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_page_title', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_address_1', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_address_2', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_city', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_state', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_postcode', true);
                    $vendor_address[] = get_user_meta($vendor->id, '_vendor_country', true);

                    $vendor_address = array_filter($vendor_address);

                    if (count($vendor_address) > 0):
                        $term = implode(", ", $vendor_address);
                        ?>
                        <a href="https://www.google.com/maps?q=<?php echo $term ?>"><?php echo $term ?></a>
                    <?php endif; ?>
                </div>


                <div class="price">

                    <?php echo $_product->get_price_html(); ?>
                </div>
                <div class="distance">
                    <?php


                    if (isset($areaDistance[$more_product['product_id']])) {
                        echo round($areaDistance[$more_product['product_id']], 2)." Kms";
                    }
                    ?>
                </div>
                <div class="action">
                    <a data-id="<?php echo $_product->get_id(); ?>"
                       class="buttongap add_to_vendor_cart button"><?php echo __('Add To Cart', 'dc-woocommerce-multi-vendor'); ?></a>
                </div>

                <?php
            }


        }
    }
}

function km_more_vendor_by_distance()
{

    @session_start();
    $lat = ($_SESSION['lat']) ? $_SESSION['lat'] : '';
    $lang = ($_SESSION['lang']) ? $_SESSION['lang'] : '';


    global $WCMp, $post, $wpdb;
    $results_str = '';

    if (isset($_REQUEST['vendor']) && !empty($_REQUEST['vendor']) && wc_get_product($_REQUEST['vendor']) && get_post_status($_REQUEST['vendor']) == 'publish') {
        $p_parent_id = get_post_meta($post->ID, 'p_parent_id', true);
        $querystr = "select * from " . KM_MAP_TABLE . " where product_id = '{$p_parent_id}'";
    } else {
        $querystr = "select * from " . KM_MAP_TABLE . " where product_id = '{$post->ID}'";
    }


    $results_obj_arr = $wpdb->get_results($querystr);


    if (isset($results_obj_arr) && count($results_obj_arr) > 0) {
        $results_str = $results_obj_arr[0]->product_ids;
    }
    $areaDistance = array();

    if (!empty($results_str)) {
        $product_id_arr = explode(',', $results_str);

        if (!empty($lat) && !empty($lang)) {
            $dis_loc = "SELECT DISTINCT object_id ,  6371 * 2 * ASIN(SQRT(
                POWER(SIN((l.lat - abs('{$lat}')) * pi()/180 / 2),
                2) + COS(l.lat * pi()/180 ) * COS(abs('{$lat}') *
                pi()/180) * POWER(SIN((l.lang - '{$lang}') *
                pi()/180 / 2), 2) )) as distance FROM {$wpdb->term_relationships} as tr
              inner join {$wpdb->termmeta} as tm on tr.term_taxonomy_id = tm.term_id and tm.meta_key = '_vendor_user_id'
              left join " . KM_LATLANG_TABLE . " as l on tm.meta_value = l.vendor_id and tm.meta_key = '_vendor_user_id'
               where object_id in ({$results_str}) ORDER BY  6371 * 2 * ASIN(SQRT(
                POWER(SIN((l.lat - abs('{$lat}')) * pi()/180 / 2),
                2) + COS(l.lat * pi()/180 ) * COS(abs('{$lat}') *
                pi()/180) * POWER(SIN((l.lang - '{$lang}') *
                pi()/180 / 2), 2) ))";

            $dis_results = $wpdb->get_results($dis_loc);

            if ($dis_results) {
                $product_id_arr = array();
                foreach ($dis_results as $dis_result):
                    $product_id_arr[] = $dis_result->object_id;
                    $areaDistance[$dis_result->object_id] = $dis_result->distance;
                endforeach;
            }

        }


        if (count($product_id_arr) > 0) {
            $args = array(
                'posts_per_page' => -1,
                'offset' => 0,
                'orderby' => 'post__in',
                'order' => 'DESC',
                'post_type' => 'product',
                'post__in' => $product_id_arr,
                'post_status' => 'publish',
                'suppress_filters' => true
            );
            $results = get_posts($args);

            $i = 0;
            $more_product_array = array();
            foreach ($results as $result) {
                $vendor_data = get_wcmp_product_vendors($result->ID);
                $_product = wc_get_product($result->ID);
                $other_user = new WP_User($result->post_author);
                if ($_product->is_visible() && !is_user_wcmp_pending_vendor($other_user) && !is_user_wcmp_rejected_vendor($other_user)) {
                    if ($vendor_data) {
                        if (isset($vendor_data->user_data->data->display_name)) {
                            $more_product_array[$i]['seller_name'] = $vendor_data->user_data->data->display_name;
                            $more_product_array[$i]['is_vendor'] = 1;
                            $terms = get_the_terms($result, 'dc_vendor_shop');
                            if (!empty($terms)) {
                                $more_product_array[$i]['shop_link'] = get_term_link($terms[0], 'dc_vendor_shop');
                                $more_product_array[$i]['rating_data'] = wcmp_get_vendor_review_info($terms[0]->term_id);
                            }
                        }
                    } else {
                        $more_product_array[$i]['seller_name'] = $other_user->data->display_name;
                        $more_product_array[$i]['is_vendor'] = 0;
                        $more_product_array[$i]['shop_link'] = get_permalink(wc_get_page_id('shop'));
                        $more_product_array[$i]['rating_data'] = 'admin';
                    }
                    $currency_symbol = get_woocommerce_currency_symbol();
                    $regular_price_val = $_product->get_regular_price();
                    $sale_price_val = $_product->get_sale_price();
                    $price_val = $_product->get_price();
                    $more_product_array[$i]['product_name'] = $result->post_title;
                    $more_product_array[$i]['regular_price_val'] = $regular_price_val;
                    $more_product_array[$i]['sale_price_val'] = $sale_price_val;
                    $more_product_array[$i]['price_val'] = $price_val;
                    $more_product_array[$i]['product_id'] = $result->ID;
                    $more_product_array[$i]['product_type'] = $_product->get_type();
                    if ($_product->get_type() == 'variable') {
                        $more_product_array[$i]['_min_variation_price'] = get_post_meta($result->ID, '_min_variation_price', true);
                        $more_product_array[$i]['_max_variation_price'] = get_post_meta($result->ID, '_max_variation_price', true);
                        $variable_min_sale_price = get_post_meta($result->ID, '_min_variation_sale_price', true);
                        $variable_max_sale_price = get_post_meta($result->ID, '_max_variation_sale_price', true);
                        $more_product_array[$i]['_min_variation_sale_price'] = $variable_min_sale_price ? $variable_min_sale_price : $more_product_array[$i]['_min_variation_price'];
                        $more_product_array[$i]['_max_variation_sale_price'] = $variable_max_sale_price ? $variable_max_sale_price : $more_product_array[$i]['_max_variation_price'];
                        $more_product_array[$i]['_min_variation_regular_price'] = get_post_meta($result->ID, '_min_variation_regular_price', true);
                        $more_product_array[$i]['_max_variation_regular_price'] = get_post_meta($result->ID, '_max_variation_regular_price', true);
                    }
                    $i++;
                }
            }


            if (count($more_product_array) > 0):
                ?>
                <div>
                    <div class="name">Name</div>
                    <div class="name">Address</div>
                    <div class="name">Price</div>
                    <?php if (count($areaDistance) > 0) { ?>
                        <div class="name">Distance</div>
                    <?php } ?>
                    <div class="name">Action</div>
                </div>

            <?php

            endif;

            foreach ($more_product_array as $more_product) {
                $_product = wc_get_product($more_product['product_id']);
                ?>
                <div>

                    <div class="name">
                        <a href="<?php echo $more_product['shop_link']; ?>"
                           class="wcmp_seller_name"><?php echo $more_product['seller_name']; ?></a>
                        <?php do_action('after_wcmp_singleproductmultivendor_vendor_name', $more_product['product_id'], $more_product); ?>
                    </div>
                    <div class="address">



                        <?php
                        $vendor = get_wcmp_product_vendors($_product->get_id());
                        $vendor_address = array();
                        $vendor_address[] = get_user_meta($vendor->id, '_vendor_page_title', true);
                        $vendor_address[] = get_user_meta($vendor->id, '_vendor_address_1', true);
                        $vendor_address[] = get_user_meta($vendor->id, '_vendor_address_2', true);
                        $vendor_address[] = get_user_meta($vendor->id, '_vendor_city', true);
                        $vendor_address[] = get_user_meta($vendor->id, '_vendor_state', true);
                        $vendor_address[] = get_user_meta($vendor->id, '_vendor_postcode', true);
                        $vendor_address[] = get_user_meta($vendor->id, '_vendor_country', true);

                        $vendor_address = array_filter($vendor_address);

                        if (count($vendor_address) > 0):
                            $term = implode(", ", $vendor_address);
                            ?>
                            <a href="https://www.google.com/maps?q=<?php echo $term ?>"><?php echo $term ?></a>
                        <?php endif; ?>
                    </div>


                    <div class="price">

                        <?php echo $_product->get_price_html(); ?>
                    </div>
                    <div class="distance">
                        <?php


                        if (isset($areaDistance[$more_product['product_id']])) {
                            echo round($areaDistance[$more_product['product_id']], 2)." Kms";
                        }
                        ?>
                    </div>
                    <div class="action">
                        <a data-id="<?php echo $_product->get_id(); ?>"
                           class="buttongap add_to_vendor_cart button"><?php echo __('Add To Cart', 'dc-woocommerce-multi-vendor'); ?></a>
                    </div>

                </div>

                <?php
            }


        }
    }


}

function km_distance_order_by($join)
{
    global $wp_query, $wpdb;

    if (!empty($wp_query)) {
        $join .= "LEFT JOIN " . KM_LATLANG_TABLE . " ON $wpdb->posts.ID = $wpdb->postmeta.post_id ";
    }

    return $join;
}