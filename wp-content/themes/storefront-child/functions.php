<?php

/**
 * Storefront automatically loads the core CSS even if using a child theme as it is more efficient
 * than @importing it in the child theme style.css file.
 *
 * Uncomment the line below if you'd like to disable the Storefront Core CSS.
 *
 * If you don't plan to dequeue the Storefront Core CSS you can remove the subsequent line and as well
 * as the sf_child_theme_dequeue_style() function declaration.
 */
//add_action( 'wp_enqueue_scripts', 'sf_child_theme_dequeue_style', 999 );

/**
 * Dequeue the Storefront Parent theme core CSS
 */
function sf_child_theme_dequeue_style()
{
    wp_dequeue_style('storefront-style');
    wp_dequeue_style('storefront-woocommerce-style');
}

/**
 * Note: DO NOT! alter or remove the code above this text and only add your custom PHP functions below this text.
 */


//enqueues our external font awesome stylesheet
function enqueue_our_required_stylesheets()
{
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');
}

add_action('wp_enqueue_scripts', 'enqueue_our_required_stylesheets');


add_filter("mime_types", "add_csv_plain");
function add_csv_plain($mime_types)
{

    unset($mime_types['txt|asc|c|cc|h|srt']);
    $mime_types['txt|asc|c|cc|h|srt|csv'] = 'text/plain';

    return $mime_types;
}

add_filter("woocommerce_csv_product_import_valid_filetypes", "add_csv_plain_woocommerce");
function add_csv_plain_woocommerce()
{
    return [
        'txt|csv' => 'text/plain',
        'csv' => 'text/csv',
    ];
}

add_filter('widget_text', 'do_shortcode');


function twentyfifteen_widgets_init()
{
    register_sidebar(array(
        'name' => __('Header Top Widget', ''),
        'id' => 'sidebar-top-header',
        'description' => __('Add widgets here to appear in your sidebar.', ''),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
        'name' => __('Header Quick Menu Widget', ''),
        'id' => 'sidebar-quick-menu',
        'description' => __('Add widgets here to appear in your sidebar.', ''),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
        'name' => __('Footer Subscribe Widget Area', ''),
        'id' => 'sidebar-footer-subscribe',
        'description' => __('Add widgets here to appear in your sidebar.', ''),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
    register_sidebar(array(
        'name' => __('Footer Copyright Widget', ''),
        'id' => 'sidebar-footer-copyright',
        'description' => __('Add widgets here to appear in your sidebar.', ''),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ));
}

add_action('widgets_init', 'twentyfifteen_widgets_init');
require_once "kinex/kminit.php";

if (!function_exists('storefront_post_header')) {
    /**
     * Display the post header with a link to the single post
     *
     * @since 1.0.0
     */
    function storefront_post_header()
    {
        ?>
        <header class="entry-header">
            <?php
            if (is_single()) {

                the_title('<h1 class="entry-title">', '</h1>');
                storefront_posted_on();
            } else {
                if ('post' == get_post_type()) {
                    storefront_posted_on();
                }

                the_title(sprintf('<h2 class="alpha entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h2>');
            }
            ?>
        </header><!-- .entry-header -->
        <?php
    }
}

add_filter('add_to_cart_fragments', 'km_header_add_to_cart_fragment');

function km_header_add_to_cart_fragment($fragments)
{
    global $woocommerce;

    ob_start();


    if ($woocommerce->cart->cart_contents_count > 0):
        ?>
        <h2 class="cart-custom-contents">You have <?php echo $woocommerce->cart->cart_contents_count ?> item in your
            cart.</h2>
        <?php
    endif;
    $fragments['h2.cart-custom-contents'] = ob_get_clean();

    if ($woocommerce->cart->cart_contents_count < 1):
        ob_start();


        ?>
        <p class="cart-custom-contents">Your cart is currently empty.</p>
        <?php

        $fragments['div.cart-empty-fragemnts'] = ob_get_clean();
    endif;
    return $fragments;

}


add_action('pre_get_posts', 'allow_product_post_on_search');
function allow_product_post_on_search($wp_query_obj)
{

    if ($wp_query_obj->is_search()) {
        $wp_query_obj->set('post_type', array('product', 'post'));
    }
 
    return $wp_query_obj;
}
 

add_filter('woocommerce_form_field_args', 'km_woocommerce_form_field_args');

function km_woocommerce_form_field_args($args)
{
    $args['return'] = false;
    return $args;
}

//add_filter('woocommerce_form_field_country', 'km_checkout_fields');
function km_checkout_fields($field, $key, $args, $value)
{

    if ($args['required']) {
        $args['class'][] = 'validate-required';
        $required = ' <abbr class="required" title="' . esc_attr__('required', 'woocommerce') . '">*</abbr>';
    } else {
        $required = '';
    }

    if (is_string($args['label_class'])) {
        $args['label_class'] = array($args['label_class']);
    }

    if (is_null($value)) {
        $value = $args['default'];
    }

    // Custom attribute handling
    $custom_attributes = array();
    $args['custom_attributes'] = array_filter((array)$args['custom_attributes']);

    if ($args['maxlength']) {
        $args['custom_attributes']['maxlength'] = absint($args['maxlength']);
    }

    if (!empty($args['autocomplete'])) {
        $args['custom_attributes']['autocomplete'] = $args['autocomplete'];
    }

    if (true === $args['autofocus']) {
        $args['custom_attributes']['autofocus'] = 'autofocus';
    }

    if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
        foreach ($args['custom_attributes'] as $attribute => $attribute_value) {
            $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
        }
    }

    if (!empty($args['validate'])) {
        foreach ($args['validate'] as $validate) {
            $args['class'][] = 'validate-' . $validate;
        }
    }

    $field = '';
    $label_id = $args['id'];
    $sort = $args['priority'] ? $args['priority'] : '';
    $field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr($sort) . '">%3$s</p>';


    $countries = 'shipping_country' === $key ? WC()->countries->get_shipping_countries() : WC()->countries->get_allowed_countries();

    if (1 === sizeof($countries)) {

        $field .= '<strong>' . current(array_values($countries)) . '</strong>';

        $field .= '<input type="hidden" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" value="' . current(array_keys($countries)) . '" ' . implode(' ', $custom_attributes) . ' class="country_to_state" />';

    } else {

        $field = '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="country_to_state country_select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . '>' . '<option value="">' . esc_html__('Select a country&hellip;', 'woocommerce') . '</option>';

        foreach ($countries as $ckey => $cvalue) {
            $field .= '<option value="' . esc_attr($ckey) . '" ' . selected($value, $ckey, false) . '>' . $cvalue . '</option>';
        }

        $field .= '</select>';

        $field .= '<noscript><input type="submit" name="woocommerce_checkout_update_totals" value="' . esc_attr__('Update country', 'woocommerce') . '" /></noscript>';

    }


    if (!empty($field)) {
        $field_html = '';

        if ($args['label'] && 'checkbox' != $args['type']) {
            $field_html .= '<label for="' . esc_attr($label_id) . '" class="' . esc_attr(implode(' ', $args['label_class'])) . '">' . $args['label'] . $required . '</label>';
        }

        $field_html .= $field;

        if ($args['description']) {
            $field_html .= '<span class="description">' . esc_html($args['description']) . '</span>';
        }

        $container_class = esc_attr(implode(' ', $args['class']));
        $container_id = esc_attr($args['id']) . '_field';
        $field = sprintf($field_container, $container_class, $container_id, $field_html);
    }


    return $field;


}
//add_filter('wp_prepare_attachment_for_js', 'wpse_110060_image_sizes_js', 10, 3);