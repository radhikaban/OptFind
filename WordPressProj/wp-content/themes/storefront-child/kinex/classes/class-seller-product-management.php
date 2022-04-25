<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/7/2017
 * Time: 12:59 PM
 */

class KM_SellerProduct
{

    public function __construct()
    {
        global $WCMp;

        $this->init_table();

        $this->remove_action();
        $this->init_action();
        $this->init_filter();

    }

    public function remove_action()
    {
        add_action('init', array($this, 'remove_unused_action_hooks'));
    }

    function km_return_false()
    {
        return false;
    }


    function remove_unused_action_hooks()
    {
        global $WCMp;


        remove_action('storefront_single_post', 'storefront_post_meta', 20);
        remove_action('storefront_single_post', 'storefront_post_content', 30);


        remove_action('storefront_post_content_before', 'storefront_post_thumbnail', 10);


        add_action('storefront_single_post', 'storefront_post_meta', 30);
        add_action('storefront_single_post', 'storefront_post_content', 20);

        add_action('storefront_single_post', 'storefront_post_thumbnail', 8);

        remove_action('woocommerce_before_shop_loop', 'storefront_sorting_wrapper', 9);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_pagination', 30);
        remove_action('woocommerce_before_shop_loop', 'storefront_sorting_wrapper_close', 31);
        remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');


        add_action('woocommerce_before_main_content', 'storefront_sorting_wrapper', 5);
        add_action('woocommerce_before_main_content', 'woocommerce_catalog_ordering', 6);

        add_action('woocommerce_before_main_content', 'storefront_sorting_wrapper_close', 9);


        remove_action('woocommerce_after_shop_loop', 'woocommerce_catalog_ordering', 10);
        remove_action('woocommerce_after_shop_loop', 'woocommerce_result_count', 20);


        add_action('km_woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
        add_action('km_woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
        add_action('km_woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5);
        remove_action('storefront_page', 'storefront_page_header', 10);

        if ($WCMp->vendor_caps->vendor_frontend_settings('enable_vendor_tab')) {
            remove_filter('woocommerce_product_tabs', array($WCMp->product, 'product_vendor_tab'));

        }
        remove_action('woocommerce_after_shop_loop_item_title', array($WCMp->vendor_caps, 'wcmp_after_add_to_cart_form'), 30);

        remove_action('woocommerce_product_meta_start', array($WCMp->vendor_caps, 'wcmp_after_add_to_cart_form'), 25);

        if ($WCMp->vendor_caps->vendor_frontend_settings('enable_vendor_tab')) {
            add_filter('woocommerce_product_tabs', array($this, 'product_vendor_tab'));

        }


    }


    /**
     *  Init Action
     */

    public function init_action()
    {
        global $WCMp;
        /**
         * Get product first vendor
         */
        add_action('template_redirect', array($this, 'change_product_id'), 100);
        /**
         * Map data delete and create product
         */
        add_action('delete_post', array($this, 'remove_product_from_multiple_seller_mapping'), 10);
        add_action('trashed_post', array($this, 'remove_product_from_multiple_seller_mapping'), 10);
        add_action('untrash_post', array($this, 'restore_multiple_seller_mapping'), 10);


        $settings_policies = get_option('wcmp_general_policies_settings_name');
        if (get_wcmp_vendor_settings('is_policy_on', 'general') == 'Enable') {
            if ((isset($settings_policies['is_cancellation_on']) || isset($settings_policies['is_refund_on']) || isset($settings_policies['is_shipping_on'])) && (isset($settings_policies['is_cancellation_product_level_on']) || isset($settings_policies['is_refund_product_level_on']) || isset($settings_policies['is_shipping_product_level_on']))) {
                $current_user_id = get_current_user_id();
                if ((is_user_wcmp_vendor($current_user_id) && (isset($settings_policies['can_vendor_edit_cancellation_policy']) || isset($settings_policies['can_vendor_edit_refund_policy']) || isset($settings_policies['can_vendor_edit_shipping_policy']))) || current_user_can('manage_woocommerce')) {
                    add_action('km_woocommerce_product_data_panels', array(&$this, 'output_policies_tab'), 30, 2);
                    add_action('save_post', array(&$this, 'process_policies_data'));
                }
            }
        }


        /**
         * Show only main product
         */


        // add_action('woocommerce_product_query', array($this, 'km_discourage_vendor_product_query'), 60);
        // add_filter('berocket_aapf_listener_wp_query_args', array($this, 'km_discourage_vendor_product_query_ajax'), 60);

        /**
         * Product meta show vendrod name
         */

        add_action('woocommerce_product_meta_start', array($this, 'wcmp_after_add_to_cart_form'), 25);
    }

    function process_policies_data($post_id)
    {


        $post = get_post($post_id);

        if ($post->post_type == 'product') {
            if (isset($_POST['_wcmp_enable_policy_tab'])) {
                update_post_meta($post_id, '_wcmp_enable_policy_tab', $_POST['_wcmp_enable_policy_tab']);
            } else {
                update_post_meta($post_id, '_wcmp_enable_policy_tab', '');
            }
            if (isset($_POST['_wcmp_cancallation_policy'])) {
                update_post_meta($post_id, '_wcmp_cancallation_policy', $_POST['_wcmp_cancallation_policy']);
            }
            if (isset($_POST['_wcmp_refund_policy'])) {
                update_post_meta($post_id, '_wcmp_refund_policy', $_POST['_wcmp_refund_policy']);
            }
            if (isset($_POST['_wcmp_shipping_policy'])) {
                update_post_meta($post_id, '_wcmp_shipping_policy', $_POST['_wcmp_shipping_policy']);
            }
        }
    }

    function output_policies_tab($product, $read_only = false)
    {
        global $post, $WCMp, $woocommerce;

        $WCMp->template->get_template('vendor-dashboard/seller-product-manager/manage-product-blocks/vendor-policy.php', array('product' => $product, 'read_only' => $read_only));
    }

    /**
     * Add vendor tab on single product page
     *
     * @return void
     */
    function product_vendor_tab($tabs)
    {
        global $product, $WCMp;


        if ($product) {
            if (isset($_GET['vendor']) && !empty($_GET['vendor'])) {
                $vendor = get_wcmp_product_vendors($_GET['vendor']);
            } else {
                $vendor = get_wcmp_product_vendors($product->get_id());
            }

            if ($vendor) {
                $title = __('Vendor', 'dc-woocommerce-multi-vendor');
                $tabs['vendor'] = array(
                    'title' => $title,
                    'priority' => 20,
                    'callback' => array($this, 'woocommerce_product_vendor_tab')
                );
            }
        }


        return $tabs;
    }


    /**
     * Add vendor tab html
     *
     * @return void
     */
    function woocommerce_product_vendor_tab()
    {
        global $woocommerce, $WCMp;
        $WCMp->template->get_template('vendor_tab.php');
    }


    public function wcmp_after_add_to_cart_form()
    {
        global $post;
        global $WCMp;


        if ($WCMp->vendor_caps->vendor_frontend_settings('sold_by_catalog')) {
            if (isset($_GET['vendor']) && !empty($_GET['vendor'])) {
                $vendor = get_wcmp_product_vendors($_GET['vendor']);
            } else {
                $vendor = get_wcmp_product_vendors($post->ID);
            }

            $general_cap = isset($WCMp->vendor_caps->frontend_cap['sold_by_text']) ? $WCMp->vendor_caps->frontend_cap['sold_by_text'] : '';
            if (!$general_cap)
                $general_cap = __('Sold By', 'dc-woocommerce-multi-vendor');
            if ($vendor) {
                echo '<a class="by-vendor-name-link" style="display: block;" href="' . $vendor->permalink . '">' . $general_cap . ' ' . $vendor->user_data->display_name . '</a>';
                do_action('after_sold_by_text_shop_page', $vendor);
            }
        }
    }

    public function km_discourage_vendor_product_query($q)
    {
        global $wpdb;

        if (is_tax('dc_vendor_shop')) {
            return;
        }

        if (is_tax('product_cat', 'deal-of-the-days')) {
            return;
        }


        $meta_query = $q->get('meta_query');


        $meta_query[] = array(
            'key' => 'p_parent_id',
            'compare' => 'NOT EXISTS'
        );
        $q->set('meta_query', $meta_query);

    }

    function km_deal_of_the_days($args)
    {
        $args['product_taxonomy'] = '';
    }

    public function km_discourage_vendor_product_query_ajax($args)
    {
        global $wpdb;


        if (is_tax('dc_vendor_shop')) {
            return $args;
        }


        $product_taxonomy = explode('|', $_POST['product_taxonomy']);

        if (isset($_REQUEST['product_cat']) && $_REQUEST['product_cat'] != 'deal-of-the-days') {
            $args['meta_query'][] = array(
                'key' => 'p_parent_id',
                'compare' => 'NOT EXISTS'
            );

        }
        return $args;
    }

    public function change_product_id()
    {
        global $post, $wpdb, $wp;


        if (is_singular('product') && $post->post_status == 'publish') {

            $sql_query = "select * from " . KM_MAP_TABLE . " where product_id = {$post->ID} && is_active=1";
            $result = $wpdb->get_row($sql_query);

            if (!isset($_GET['vendor']) || empty($_GET['vendor'])) {

                if (count($result) > 0) {

                    $product_ids = $result->product_ids;

                    if (!empty($product_ids)) {
                        $p_ids_arr = explode(',', $product_ids);
                        $p_ids_arr = array_values(array_filter($p_ids_arr));
                        if (is_array($p_ids_arr) && count($p_ids_arr) > 0) {
                            foreach ($p_ids_arr as $p_id) {
                                $product = wc_get_product($p_id);
                                if ($product) {
                                    $permalink = get_permalink();
                                    $url = add_query_arg('vendor', $p_id, $permalink);
                                    wp_redirect($url);
                                    die;
                                }
                            }
                        }
                    }

                } else {
                    $parent_id = get_post_meta($post->ID, 'p_parent_id', true);

                    if (!empty($parent_id)) {
                        if (get_post_status($parent_id) == 'publish') {
                            $product = wc_get_product($parent_id);
                            if ($product) {
                                $permalink = get_permalink($parent_id);
                                $url = add_query_arg('vendor', $post->ID, $permalink);
                                wp_redirect($url);
                                die;
                            }
                        }

                    }
                }
            } else {


                if ($post->post_status == 'publish') {
                    $parent_id = get_post_meta($_GET['vendor'], 'p_parent_id', true);
                    if (!wc_get_product($_GET['vendor'])) {
                        $permalink = get_permalink($post->ID);
                        wp_redirect($permalink);
                        die;
                    } else if ($parent_id != $post->ID || get_post_status($_GET['vendor']) != 'publish') {
                        $permalink = get_permalink($post->ID);
                        wp_redirect($permalink);
                        die;
                    }
                } else {
                    $permalink = get_permalink($post->ID);
                    wp_redirect($permalink);
                    die;
                }

            }
        }

    }


    /**
     *
     * Init Filter
     *
     */

    public
    function init_filter()
    {
        add_filter('woocommerce_product_tabs', array($this, 'product_single_product_multivendor_tab'), 60);
    }

    /**
     * Create Table
     */

    public
    function init_table()
    {
        global $wpdb;
        if ($wpdb->get_var("show tables like '" . KM_MAP_TABLE . "'") != KM_MAP_TABLE) {
            $sSql = "CREATE TABLE IF NOT EXISTS " . KM_MAP_TABLE . " (";
            $sSql .= "ID INT NOT NULL AUTO_INCREMENT ,";
            $sSql .= "product_id int ,";
            $sSql .= "product_ids TEXT  ,";
            $sSql .= "is_active tinyint(1) default 1  ,";
            $sSql .= "PRIMARY KEY ( ID ) ";
            $sSql .= ")";
            $wpdb->query($sSql);
        }

        global $wpdb;
        if ($wpdb->get_var("show tables like '" . KM_LATLANG_TABLE . "'") != KM_LATLANG_TABLE) {
            $sSql = "CREATE TABLE IF NOT EXISTS " . KM_LATLANG_TABLE . " (";
            $sSql .= "ID INT NOT NULL AUTO_INCREMENT ,";
            $sSql .= "vendor_id int ,";
            $sSql .= "lat varchar(50)  ,";
            $sSql .= "lang varchar(50)  ,";
            $sSql .= "is_active tinyint(1) default 1  ,";
            $sSql .= "PRIMARY KEY ( ID ) ";
            $sSql .= ")";
            $wpdb->query($sSql);
        }
    }


    /***
     * Remove Product from multiple vendor mapping
     * @param $post_id
     */

    public
    function remove_product_from_multiple_seller_mapping($post_id)
    {
        global $WCMp, $wpdb;


        $product_to_be_deleted = get_post($post_id);
        $post_type = $product_to_be_deleted->post_type;
        if ($post_type == 'product') {
            $parent_id = get_post_meta($product_to_be_deleted->ID, 'p_parent_id', true);
            if (!empty($parent_id)) {
                $sql_query = "select * from " . KM_MAP_TABLE . " where product_id = {$parent_id}";
                $results = $wpdb->get_results($sql_query);
                if (count($results) > 0) {
                    foreach ($results as $result) {
                        $product_ids = $result->product_ids;
                        if (!empty($product_ids)) {
                            $p_ids_arr = explode(',', $product_ids);
                            if (is_array($p_ids_arr) && !empty($p_ids_arr) && count($p_ids_arr) > 0) {

                                if (($key = array_search($post_id, $p_ids_arr)) !== false) {
                                    unset($p_ids_arr[$key]);
                                }
                                $p_ids = implode(',', $p_ids_arr);
                                $update_query = "update " . KM_MAP_TABLE . " set product_ids='{$p_ids}'  where ID = {$result->ID}";
                                $wpdb->query($update_query);

                            }
                        }
                    }
                }

            } else {
                $sql_query = "select * from " . KM_MAP_TABLE . " where product_id = '{$product_to_be_deleted->ID}'";
                $results = $wpdb->get_row($sql_query);
                if (count($results) > 0) {

                    $update_query = "update " . KM_MAP_TABLE . " set is_active=0  where ID = {$results->ID}";
                    $wpdb->query($update_query);


                }
            }
        }

    }


    /***
     * Restore Multiple seller mapping
     * @param $post_id
     */
    public
    function restore_multiple_seller_mapping($post_id)
    {
        global $WCMp, $wpdb;
        $product_to_be_restored = get_post($post_id);
        $post_type = $product_to_be_restored->post_type;
        if ($post_type == 'product') {
            $parent_id = get_post_meta($product_to_be_restored->ID, 'p_parent_id', true);

            if (!empty($parent_id)) {
                $sql_query = "select * from " . KM_MAP_TABLE . " where product_id = {$parent_id}";
                $results = $wpdb->get_results($sql_query);
                if (count($results) > 0) {
                    foreach ($results as $result) {
                        $product_ids = $result->product_ids;

                        if (!empty($product_ids)) {
                            $p_ids_arr = explode(',', $product_ids);
                            $p_ids_arr[] = $post_id;
                            $p_ids = implode(',', $p_ids_arr);
                            $update_query = "update " . KM_MAP_TABLE . " set product_ids='{$p_ids}'  where ID = {$result->ID}";
                            $wpdb->query($update_query);
                        } else {
                            $update_query = "update " . KM_MAP_TABLE . " set product_ids='{$post_id}'  where ID = {$result->ID}";
                            $wpdb->query($update_query);
                        }
                    }
                } else {
                    $parent_id = get_post_meta($post_id, 'p_parent_id', true);
                    if (!empty($parent_id)) {
                        update_data_to_products_map_table($parent_id, $post_id);
                    }
                }
            } else {

                $sql_query = "select * from " . KM_MAP_TABLE . " where product_id = '{$product_to_be_restored->ID}'";
                $results = $wpdb->get_row($sql_query);
                if (count($results) > 0) {
                    $update_query = "update " . KM_MAP_TABLE . " set is_active=1  where ID = {$results->ID}";
                    $wpdb->query($update_query);
                }
            }
        }
    }


    public
    function product_single_product_multivendor_tab($tabs)
    {

        $tabs['singleproductmultivendor'] = array(
            'title' => "More Offers",
            'priority' => 80,
            'callback' => array($this, 'km_product_single_product_multivendor_tab_template')
        );
        return $tabs;
    }

    public
    function km_product_single_product_multivendor_tab_template()
    {

        global $woocommerce, $WCMp, $post, $wpdb;

        $more_product_array = array();
        $results = array();
        $more_products = $this->get_multiple_vendors_array_for_single_product($post->ID);
        $more_product_array = $more_products['more_product_array'];
        $results = $more_products['results'];
        $WCMp->template->get_template('single-product/multiple_vendors_products.php', array('results' => $results, 'more_product_array' => $more_product_array));
    }

    public
    function get_multiple_vendors_array_for_single_product($post_id)
    {
        global $woocommerce, $WCMp, $wpdb;
        $post = get_post($post_id);
        $results_str = '';
        $p_parent_id = '';
        if (isset($_REQUEST['vendor']) && !empty($_REQUEST['vendor'])) {
            $p_parent_id = get_post_meta($post_id, 'p_parent_id', true);

            $query_str = "select  * from " . KM_MAP_TABLE . " where product_id = '{$p_parent_id}'";
        } else {
            $query_str = "select  * from " . KM_MAP_TABLE . " where product_id = '{$post_id}'";
        }


        $results_obj_arr = $wpdb->get_results($query_str);

        if (isset($results_obj_arr) && count($results_obj_arr) > 0) {
            $results_str = $results_obj_arr[0]->product_ids;
        }
        $product_id_arr = explode(',', $results_str);


        if (isset($_GET['vendor'])) {
            if (($key = array_search($_GET['vendor'], $product_id_arr)) !== false) {
                unset($product_id_arr[$key]);
            }
        }

        $results = array();

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
        }
        $i = 0;
        $more_product_array = array();
        foreach ($results as $result) {
            $vendor_data = get_wcmp_product_vendors($result->ID);
            $_product = wc_get_product($result->ID);
            $other_user = new WP_User($result->post_author);
            if ($_product->is_visible() && !is_user_wcmp_pending_vendor($other_user) && !is_user_wcmp_rejected_vendor($other_user) && $post->post_author != $result->post_author) {
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

        return array('results' => $results, 'more_product_array' => $more_product_array);
    }


    public
    function save_product($vendor_id, $request)
    {
        $this->insert_product($vendor_id, $request['product_manager']);
    }

    public
    function insert_product($vendor_id, $product_data)
    {
        $ID = array();
        if (isset($product_data['product_id'])) {
            $ID['ID'] = $product_data['product_id'];
        }
        $post = array( // Set up the basic post data to insert for our product
            'post_author' => $vendor_id,
            'post_status' => 'publish',
            'post_title' => $product_data['product_name'],
            'post_type' => 'product'
        );
        $post = array_merge($post, $ID);


        $post_id = wp_update_post($post); // Insert the post returning the new post id

        if (!$post_id) // If there is no post id something has gone wrong so don't proceed
        {
            return false;
        }
        $dc_vendor_id = get_user_meta(get_current_user_id(), '_vendor_term_id', true);
        $term = get_term($dc_vendor_id, 'dc_vendor_shop');

        wp_set_object_terms($post_id, $term->term_id, 'dc_vendor_shop'); // Set up its categories

        $product = wc_get_product($post_id);


        if (isset($product_data['featured_product'])) {
            $product->set_featured(true);
        } else {
            $product->set_featured(false);
        }


        if (isset($product_data['is_variable']) && !empty($product_data['is_variable'])) {
            $this->insert_product_attributes($post_id, $product_data['available_attributes'], $product_data['variations']); // Add attributes passing the new post id, attributes & variations
            $this->insert_product_variations($post_id, $product_data['variations']); // Insert variations passing the new post id & variations
        } else {

            update_post_meta($post_id, '_regular_price', $product_data['regular_price']);

            update_post_meta($post_id, '_sale_price', $product_data['sale_price']);

            if ($product_data['sale_price'] < $product_data['regular_price'] && !empty($product_data['sale_price']) && $product_data['sale_price'] > 0) {
                update_post_meta($post_id, '_price', $product_data['sale_price']);
            } else {
                update_post_meta($post_id, '_price', $product_data['regular_price']);
            }
        }

        $product->save();
        wc_delete_product_transients($post_id);
        @session_start();
        $_SESSION['km_edit_products_date'] = "<div class='success'>Prodcuts edit sucessfully.<a href='" . esc_url(wcmp_get_vendor_dashboard_endpoint_url(apply_filters('km_seller_add_product_manager_url', 'seller-add-product-manager'), $post_id)) . "'>View Products</a>";
        wp_redirect(wcmp_get_vendor_dashboard_endpoint_url(apply_filters('km_seller_product_manager_url', 'seller-product-manager')));
        die;

    }

    public
    function insert_product_attributes($post_id, $available_attributes, $variations)
    {

        $available_attributes = array_unique($available_attributes);

        foreach ($available_attributes as $attribute) // Go through each attribute
        {
            // Set up an array to store the current attributes values.

            $values = array();
            foreach ($variations as $variation) // Loop each variation in the file
            {

               // if (!empty($variation['regular_price'])) {
                    $attribute_keys = array_keys($variation['attributes']); // Get the keys for the current variations attributes

                    foreach ($attribute_keys as $key) // Loop through each key
                    {
                        if ($key === $attribute) // If this attributes key is the top level attribute add the value to the $values array
                        {
                            $values[] = $variation['attributes'][$key];
                        }
                    }
//                }


            }


            $values = array_unique($values); // Filter out duplicate values
            wp_set_object_terms($post_id, $values, $attribute);

        }

        $product_attributes_data = array(); // Setup array to hold our product attributes data

        foreach ($available_attributes as $attribute) // Loop round each attribute
        {
            $product_attributes_data[$attribute] = array( // Set this attributes array to a key to using the prefix 'pa'

                'name' => $attribute,
                'value' => '',
                'is_visible' => '1',
                'is_variation' => '1',
                'is_taxonomy' => '1'

            );
        }

        update_post_meta($post_id, '_product_attributes', $product_attributes_data); // Attach the above array to the new posts meta data key '_product_attributes'
    }


    public
    function insert_product_variations($post_id, $variations)
    {
        foreach ($variations as $index => $variation) {

            $ID = array();
            if (isset($variation['variation_id'])) {
                $ID['ID'] = $variation['variation_id'];
            }

//            if (empty($variation['regular_price'])) {
//                continue;
//            }
            $variation_post = array( // Setup the post data for the variation
                'post_title' => 'Variation #' . $index . ' of ' . count($variations) . ' for product#' . $post_id,
                'post_name' => 'product-' . $post_id . '-variation-' . $index,
                'post_status' => 'publish',
                'post_parent' => $post_id,
                'post_type' => 'product_variation',
                'guid' => home_url() . '/?product_variation=product-' . $post_id . '-variation-' . $index
            );

            $variation_post = array_merge($variation_post, $ID);

            $variation_post_id = wp_insert_post($variation_post); // Insert the variation

            foreach ($variation['attributes'] as $attribute => $value) // Loop through the variations attributes
            {


                $attribute_term = get_term_by('slug', $value, $attribute); // We need to insert the slug not the name into the variation post meta


                update_post_meta($variation_post_id, 'attribute_' . $attribute, $attribute_term->slug);
                // Again without variables: update_post_meta(25, 'attribute_pa_size', 'small')
            }

            update_post_meta($variation_post_id, '_regular_price', $variation['regular_price']);

            update_post_meta($variation_post_id, '_sale_price', $variation['sale_price']);

            if ($variation['sale_price'] < $variation['regular_price'] && !empty($variation['sale_price']) && $variation['sale_price'] > 0) {
                update_post_meta($variation_post_id, '_price', $variation['regular_price']);
            } else {
                update_post_meta($variation_post_id, '_price', $variation['sale_price']);
            }

        }
    }


    public
    function delete_product($vendor_id, $request)
    {
        if (isset($_POST['km_delete_product']) || wp_verify_nonce($_POST['km_delete_product'], 'km-delete-product')) {

            $product = get_page_by_path($request['product-slug'], OBJECT, 'product');

            if (!empty($product)) {
                $data = wp_delete_post($product->ID);
                if (!empty($data)) {
                    return "";
                } else {
                    return "Something went wrong!.Product does not deleted";
                }
            } else {
                return "Product does not exist";
            }

        } else {
            return "Something Went Wrong! Porduct is not deleted";
        }
    }
}