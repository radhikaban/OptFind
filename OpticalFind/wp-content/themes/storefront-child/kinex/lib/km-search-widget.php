<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/26/2017
 * Time: 11:06 AM
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Product Search Widget.
 *
 * @author   WooThemes
 * @category Widgets
 * @package  WooCommerce/Widgets
 * @version  2.3.0
 * @extends  WC_Widget
 */
class KM_Product_Search extends WC_Widget
{

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->widget_cssclass = 'woocommerce widget_product_search';
        $this->widget_description = __('A Search box for products.', 'woocommerce');
        $this->widget_id = 'woocommerce_product_search';
        $this->widget_name = __('WooCommerce product search', 'woocommerce');
        $this->settings = array(
            'title' => array(
                'type' => 'text',
                'std' => '',
                'label' => __('Title', 'woocommerce'),
            ),
        );

        parent::__construct();
    }

    /**
     * Output widget.
     *
     * @see WP_Widget
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        $this->widget_start($args, $instance);

        km_get_product_search_form();

        $this->widget_end($args);
    }
}

register_widget('KM_Product_Search');


class KMSearch_Shortcode
{
    function __construct()
    {
        // We safely integrate with VC with this hook
        add_action('init', array($this, 'integrateWithVC'));

        // Use this when creating a shortcode addon
        add_shortcode('km-search', array(
            $this, 'search'));


    }

    public function integrateWithVC()
    {
        // Check if Visual Composer is installed
        if (!defined('WPB_VC_VERSION')) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array($this, 'km_show_vc_warnings'));
            return;
        }

        $available_image_sizes_adapted = array();
        $available_image_sizes = get_intermediate_image_sizes();

        foreach ($available_image_sizes as $image_size) {
            $available_image_sizes_adapted[$image_size] = $image_size;
        }

        vc_map(array(
            "name" => __("KM Search", 'vc_extend'),
            "description" => __("Show Brands List Sorting", 'vc_extend'),
            "base" => "km-search",
            "class" => "",
            'show_settings_on_create'=>false,
            "category" => "WooCommerce",
        ));

    }

    function wpse_178511_get_terms_fields($clauses, $taxonomies, $args)
    {
        if (!empty($args['surname'])) {
            global $wpdb;

            $surname_like = $wpdb->esc_like($args['surname']);

            if (!isset($clauses['where']))
                $clauses['where'] = '1=1';

            $clauses['where'] .= $wpdb->prepare(" AND t.name LIKE %s", "$surname_like%");
        }

        return $clauses;
    }


    /*
    Shortcode logic how it should be rendered
    */
    public function search($atts, $content = null)
    {
        ob_start();
        if (storefront_is_woocommerce_activated()) {
            the_widget('KM_Product_Search');
        } else {
            get_search_form();
        }


        return ob_get_clean();
    }


    /*
    Show notice if your plugin is activated but Visual Composer is not
    */
    public function km_show_vc_warnings()
    {
        echo '
        <div class="updated">
          <p>' . sprintf(__('<strong></strong> requires <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'vc_extend')) . '</p></div>';
    }
}

// Finally initialize code
new KMSearch_Shortcode();