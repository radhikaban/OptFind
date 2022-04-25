<?php
/**
 * Created by PhpStorm.
 * User: Sarab
 * Date: 11-Dec-17
 * Time: 3:55 PM
 */


/* ======= the model main class =========== */
if (!class_exists('KMCL_AddToCartButton')) {
    $_framework = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'addtocart.class.php';
    if (file_exists($_framework))
        include_once($_framework);
    else
        die ('File not found!' . $_framework);
}


class KM_CustomLenses
{


    /**
     * this holds all input objects
     */
    var $inputs;


    /**
     * @var array
     * Ajax Callbackd
     */
    protected $ajax_callbacks;

    /**
     * @var
     * Register init action
     */

    protected $register_init;


    /**
     * the static object instace
     */
    private static $ins = null;


    public static function init()
    {

        add_action('plugins_loaded', array(self::get_instance(), '_setup'));
    }

    public static function get_instance()
    {
        // create a new object if it doesn't exist.
        is_null(self::$ins) && self::$ins = new self;
        return self::$ins;
    }

    function __construct()
    {

        $this->ajax_callbacks = array(
            'km_get_lens_type',
            'km_add_to_cart',
            'km_set_locations',
            'km_get_lens_category',
            'km_get_custom_lens'// do not change this action, is for admin
            //if images not moved to confirmed dir then admin can do it manually
        );


        $this->register_init = array(
            array(
                'hook' => 'init',
                'callback' => array($this, 'register_post_init'),
                'priority' => 10,
                'params' => 1
            ),
            array(
                'hook' => 'init',
                'callback' => array($this, 'register_taxonomy_init'),
                'priority' => 10,
                'params' => 1
            ),
            array(
                'hook' => 'add_meta_boxes',
                'callback' => array($this, 'kmcl_add_meta_box'),
                'priority' => 10,
                'params' => 1
            ),
            array(
                'hook' => 'save_post',
                'callback' => array($this, 'kmcl_save_meta_box'),
                'priority' => 10,
                'params' => 1
            ),
            array(
                'hook' => 'add_meta_boxes',
                'callback' => array($this, 'kmcl_add_product_fields'),
                'priority' => 10,
                'params' => 1
            ),
            array(
                'hook' => 'woocommerce_process_product_meta',
                'callback' => array($this, 'kmcl_save_product_fields'),
                'priority' => 10,
                'params' => 1
            ),
            array(
                'hook' => 'woocommerce_single_product_summary',
                'callback' => array('KMCL_AddToCartButton', 'kmcl_add_custom_add_to_cart_button_into_single'),
                'priority' => 10,
                'params' => 1
            ),

            array(
                'hook' => 'wp_enqueue_scripts',
                'callback' => array($this, 'kmcl_add_style_scripts'),
                'priority' => 10,
                'params' => 1
            )
        );

        /*
         *
         * registering callbacks
         *
         */

        $this->do_callbacks();

        $this->register_actions();
    }


    public function register_actions()
    {
        foreach ($this->register_init as $hooks) {
            add_action($hooks['hook'], $hooks['callback'], $hooks['priority'], $hooks['params']);
        }
    }


    /**
     * Register a Custom Lenses Post type.
     *
     * @link http://codex.wordpress.org/Function_Reference/register_post_type
     */
    function register_post_init()
    {
        $labels = array(
            'name' => _x('Addons Lenses', 'custom lenses', 'wc-custom-lenses'),
            'singular_name' => _x('Addons Lens', 'custom lens', 'wc-custom-lenses'),
            'menu_name' => _x('Addons Lenses', 'custom lenses admin menu', 'wc-custom-lenses'),
            'name_admin_bar' => _x('Addons Lens', 'add new on admin bar', 'wc-custom-lenses'),
            'add_new' => _x('Add New', 'custom lense', 'wc-custom-lenses'),
            'add_new_item' => __('Add New Lens', 'wc-custom-lenses'),
            'new_item' => __('New Lens', 'wc-custom-lenses'),
            'edit_item' => __('Edit Lens', 'wc-custom-lenses'),
            'view_item' => __('View Lens', 'wc-custom-lenses'),
            'all_items' => __('All Lens', 'wc-custom-lenses'),
            'search_items' => __('Search Lenses', 'wc-custom-lenses'),
            'parent_item_colon' => __('Parent Lenses:', 'wc-custom-lenses'),
            'not_found' => __('No lenses found.', 'wc-custom-lenses'),
            'not_found_in_trash' => __('No lenses found in Trash.', 'wc-custom-lenses')
        );

        $args = array(
            'labels' => $labels,
            'description' => __('Description.', 'wc-custom-lenses'),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'cm-lenses'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments')
        );

        register_post_type('cm-lenses', $args);
    }


// create two taxonomies, category and tags for the post type "custom lenses"
    function register_taxonomy_init()
    {
        // Add new taxonomy, make it hierarchical (like categories)
        $labels = array(
            'name' => _x('Category', 'category', 'wc-custom-lenses'),
            'singular_name' => _x('Category', 'category', 'wc-custom-lenses'),
            'search_items' => __('Search Category', 'wc-custom-lenses'),
            'all_items' => __('All Categories', 'wc-custom-lenses'),
            'parent_item' => __('Parent Category', 'wc-custom-lenses'),
            'parent_item_colon' => __('Parent Category:', 'wc-custom-lenses'),
            'edit_item' => __('Edit Category', 'wc-custom-lenses'),
            'update_item' => __('Update Category', 'wc-custom-lenses'),
            'add_new_item' => __('Add New Category', 'wc-custom-lenses'),
            'new_item_name' => __('New Category', 'wc-custom-lenses'),
            'menu_name' => __('Categories', 'wc-custom-lenses'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'lenses-category'),
        );

        register_taxonomy('lenses-category', array('cm-lenses'), $args);

        $labels = array(
            'name' => _x('Type', 'type', 'wc-custom-lenses'),
            'singular_name' => _x('Type', 'type', 'wc-custom-lenses'),
            'search_items' => __('Search Type', 'wc-custom-lenses'),
            'all_items' => __('All Types', 'wc-custom-lenses'),
            'parent_item' => __('Parent Type', 'wc-custom-lenses'),
            'parent_item_colon' => __('Parent Type:', 'wc-custom-lenses'),
            'edit_item' => __('Edit Type', 'wc-custom-lenses'),
            'update_item' => __('Update Type', 'wc-custom-lenses'),
            'add_new_item' => __('Add New Type', 'wc-custom-lenses'),
            'new_item_name' => __('New Type', 'wc-custom-lenses'),
            'menu_name' => __('Types', 'wc-custom-lenses'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'lenses-types'),
        );

        register_taxonomy('lenses-types', array('cm-lenses'), $args);


        $labels = array(
            'name' => _x('Lens Index', 'type', 'wc-custom-lenses'),
            'singular_name' => _x('Lens Index', 'type', 'wc-custom-lenses'),
            'search_items' => __('Search Lens Index', 'wc-custom-lenses'),
            'all_items' => __('All Lens Index', 'wc-custom-lenses'),
            'parent_item' => __('Parent Lens Index', 'wc-custom-lenses'),
            'parent_item_colon' => __('Parent Lens Index:', 'wc-custom-lenses'),
            'edit_item' => __('Edit Lens Index', 'wc-custom-lenses'),
            'update_item' => __('Update Lens Index', 'wc-custom-lenses'),
            'add_new_item' => __('Add New Lens Index', 'wc-custom-lenses'),
            'new_item_name' => __('New Lens Index', 'wc-custom-lenses'),
            'menu_name' => __('Lens Index', 'wc-custom-lenses'),
        );

        $args = array(
            'hierarchical' => true,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'lenses-index'),
        );

        register_taxonomy('lenses-index', array('cm-lenses'), $args);


        $labels = array(
            'name' => _x('Tags', 'tags', 'wc-custom-lenses'),
            'singular_name' => _x('Tag', 'tags', 'wc-custom-lenses'),
            'search_items' => __('Search Tags', 'wc-custom-lenses'),
            'popular_items' => __('Popular Tags', 'wc-custom-lenses'),
            'all_items' => __('All Tags', 'wc-custom-lenses'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => __('Edit Tag', 'wc-custom-lenses'),
            'update_item' => __('Update Tag', 'wc-custom-lenses'),
            'add_new_item' => __('Add New Tag', 'wc-custom-lenses'),
            'new_item_name' => __('New Tag Name', 'wc-custom-lenses'),
            'separate_items_with_commas' => __('Separate tags with commas', 'wc-custom-lenses'),
            'add_or_remove_items' => __('Add or remove tags', 'wc-custom-lenses'),
            'choose_from_most_used' => __('Choose from the most used tags', 'wc-custom-lenses'),
            'not_found' => __('No tags found.', 'wc-custom-lenses'),
            'menu_name' => __('Tags', 'wc-custom-lenses'),
        );

        $args = array(
            'hierarchical' => false,
            'labels' => $labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'query_var' => true,
            'rewrite' => array('slug' => 'lenses-tag'),
        );

        register_taxonomy('lenses-tag', 'cm-lenses', $args);
    }


    function kmcl_add_product_fields()
    {

        $screens = ['product'];
        foreach ($screens as $screen) {
            add_meta_box(
                'lenses_box',           // Unique ID
                'Addon Lens',  // Box title
                array($this, 'product_fields_html_callback'),  // Content callback, must be of type callable
                $screen                   // Post type
            );
        }


    }

    function product_fields_html_callback()
    {
        echo '<div class=" product_custom_field ">';
        woocommerce_wp_checkbox(array('id' => 'kmcl_addon_lenses', 'label' => __('Addons Lenses ', 'wc-custom-lenses'), 'description' => __("Show lenses add on with cart with this product on cart page.", 'wc-custom-lenses')));
        echo "</div>";		 echo '<div class=" product_custom_field ">';		        woocommerce_wp_checkbox(array('id' => 'kmcl_add_prescription_lenses', 'label' => __('Add Prescription for Contact Lenses ', 'wc-custom-lenses'), 'description' => __("", 'wc-custom-lenses')));        echo "</div>";
    }

    public function kmcl_save_product_fields($post_id)
    {

        if (!(isset($_POST['woocommerce_meta_nonce'], $_POST['kmcl_addon_lenses']) || wp_verify_nonce(sanitize_key($_POST['woocommerce_meta_nonce']), 'woocommerce_save_data'))) {
            return false;
        }		if (!(isset($_POST['woocommerce_meta_nonce'], $_POST['kmcl_add_prescription_lenses']) || wp_verify_nonce(sanitize_key($_POST['woocommerce_meta_nonce']), 'woocommerce_save_data'))) {            return false;        }

        $product_teaser = sanitize_text_field(
            wp_unslash($_POST['kmcl_addon_lenses'])
        );				$product_prescription_lenses = sanitize_text_field(            wp_unslash($_POST['kmcl_add_prescription_lenses'])        );

        update_post_meta(
            $post_id,
            'kmcl_addon_lenses',
            esc_attr($product_teaser)
        );				update_post_meta(            $post_id,            'kmcl_add_prescription_lenses',            esc_attr($product_prescription_lenses)        );
    }


    function kmcl_add_meta_box()
    {
        $screens = ['cm-lenses'];
        foreach ($screens as $screen) {
            add_meta_box(
                'lenses_box',           // Unique ID
                'Lenses Details',  // Box title
                array($this, 'lenses_html_callback'),  // Content callback, must be of type callable
                $screen                   // Post type
            );
        }
    }


    function lenses_html_callback($post)
    {
        global $woocommerce;
        $regular_price = get_post_meta($post->ID, '_regular_price', true);
        $sale_price = get_post_meta($post->ID, '_sale_price', true);
        $lens_feature = get_post_meta($post->ID, '_lens_feature', true);
        $lens_index = get_post_meta($post->ID, '_lens_index', true);

        ?>
        <div id="general_product_data" class="panel woocommerce_options_panel" style="display: block;">
            <div class="options_group pricing show_if_simple show_if_external hidden" style="display: block;">
                <p class="form-field _regular_price_field ">
                    <label for="_regular_price">Regular price (<?php echo get_woocommerce_currency_symbol() ?>)</label>
                    <input required type="text" class="short wc_input_price" style="" name="_regular_price"
                    id="_regular_price"
                    value="<?php echo $regular_price ?>" placeholder="eq: 23.21">
                </p>
                <p class="form-field _sale_price_field ">
                    <label for="_sale_price">Sale price (<?php echo get_woocommerce_currency_symbol() ?>)</label>
                    <input type="text" class="short wc_input_price" style="" name="_sale_price" id="_sale_price"
                    value="<?php echo $sale_price ?>" placeholder="eq: 23.21">
                </p>
            </div>
        </div>


        <div id="general_product_data" class="panel woocommerce_options_panel" style="display: block;">
            <div class="options_group pricing show_if_simple show_if_external hidden" style="display: block;">
                <p class="form-field _dioptre_range _lens_index">
                    <label for="_lens_index">Lens Index</label>

                    <input value="<?php echo $lens_index ?>" type="text" class="short" name="_lens_index"
                    id="_lens_index" placeholder="eq: 1.57"/>

                </p>

            </div>
        </div>


        <div id="general_product_data" class="panel woocommerce_options_panel" style="display: block;">
            <div class="options_group pricing show_if_simple show_if_external hidden" style="display: block;">
                <p class="form-field ">
                    <label for="_lens_feature"><strong>Lens Features</strong></label>
                </p>
                <p class="form-field anti-reflect">
                    <input type="checkbox" class="short wc_input_price"
                    style="" <?php echo is_array($lens_feature) && in_array('anti-reflect', $lens_feature) ? 'checked' : '' ?>
                    name="_lens_feature[]" id="anti-reflect" value="anti-reflect">
                    <label for="anti-reflect">Anti-Reflective</label>

                    <input type="checkbox" class="short wc_input_price"
                    style="" <?php echo is_array($lens_feature) && in_array('uv-protective', $lens_feature) ? 'checked' : '' ?>
                    name="_lens_feature[]" id="uv-protective" value="uv-protective">
                    <label for="uv-protective">UV Protective</label>

                    <input type="checkbox" class="short wc_input_price"
                    style="" <?php echo is_array($lens_feature) && in_array('scratch-resist', $lens_feature) ? 'checked' : '' ?>
                    name="_lens_feature[]" id="scratch-resist" value="scratch-resist">
                    <label for="scratch-resist">Scratch Resistant</label>

                </p>
            </div>
        </div>

        <?php
    }

    /**
     * @param $post_id
     * save meta box
     */

    public static function kmcl_save_meta_box($post_id)
    {


        if (array_key_exists('_regular_price', $_POST)) {
            update_post_meta(
                $post_id,
                '_regular_price',
                $_POST['_regular_price']
            );
        }
        if (array_key_exists('_sale_price', $_POST)) {
            update_post_meta(
                $post_id,
                '_sale_price',
                $_POST['_sale_price']
            );
        }

        if (array_key_exists('_lens_feature', $_POST)) {
            update_post_meta(
                $post_id,
                '_lens_feature',
                $_POST['_lens_feature']
            );
        } else {
            delete_post_meta($post_id, '_lens_feature');
        }


        if (array_key_exists('_lens_index', $_POST)) {
            update_post_meta(
                $post_id,
                '_lens_index',
                $_POST['_lens_index']
            );
        }


    }


    function kmcl_add_style_scripts()
    {
        wp_enqueue_script('kmcl-steps-js', KMCL_PLUGIN_URL . "assets/js/jquery.steps.js");
        wp_enqueue_style('kmcl-steps-css', KMCL_PLUGIN_URL . "assets/css/jquery.steps.css");
        wp_localize_script("kmcl-steps-js", 'kmcl',
            array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'kmcl_nonce' => wp_create_nonce('kmcl_ajax_nonce_generate')
            )
        );
    }


    /*
	 * registering callback
	*/

    function do_callbacks()
    {

        foreach ($this->ajax_callbacks as $callback) {
            add_action('wp_ajax_' . $callback, array($this, $callback));
            add_action('wp_ajax_nopriv_' . $callback, array($this, $callback));
        }
    }


    function get_lens_index($right, $left)
    {


        global $wpdb;

        $terms = get_terms(array('taxonomy' => 'lenses-index'));

        $id = $this->get_id($terms, $right);


        if ($id && !isset($id['ids'])) {
            return $id;
        } else {
            return $this->get_id($terms, $left, $id, true);
        }
    }

    function get_id($terms, $dioptre, $oldIds = array(), $second = false)
    {
        $found = false;
        $ids = array();
        $id = array();
        foreach ($terms as $term) {
            $parts = explode("-", $term->name);
            $ids[] = $term->term_id;
            if (count($parts) > 1) {
                if ($dioptre >= $parts[0] && $dioptre <= $parts[1]) {
                    $id[] = $term->term_id;
                    $found = true;
                }
            } elseif ((int)$parts[0] >= $dioptre || $dioptre <= (int)$parts[0]) {
                $id[] = $term->term_id;
                $found = true;
            }
        }

        if ($found) {
            return $id;
        } elseif ($second) {
            $retun_ids = array_unique(array_merge($oldIds['ids'], $ids));
            return $retun_ids;
        } else {
            return array('ids' => $ids);
        }
    }

    /**
     * Ajax Callbacks
     */


    function km_add_to_cart()
    {

        if ('POST' !== strtoupper($_SERVER['REQUEST_METHOD']))
            return;
        if (check_ajax_referer('kmcl_ajax_nonce_generate', false, false)) {

            parse_str($_REQUEST['data'], $params);


            if (isset($_FILES) && count($_FILES) > 0) {


                $uploaded_file = wp_handle_upload($_FILES['file'], array('test_form' => false));
                if ($uploaded_file && !isset($uploaded_file['error'])) {
                    $params['prescription_url'] = $uploaded_file['url'];
                } else {

                    $response['response'] = 'error';
                    $response['message'] = $uploaded_file['error'];
                    echo json_encode($response);
                    die;
                }
            }

            $variation_id = 0;
            if (isset($params['product_id'])) {
                $product_id = $params['product_id'];
                $variation_id = ($params['variation_id']);
            } elseif (isset($params['p_id'])) {
                $product_id = $params['p_id'];
            } else {
                $response['response'] = 'error';
                $response['message'] = 'Something Went Worng';
                echo json_encode($response);
                die;
            }

            $data = WC()->cart->add_to_cart($product_id, 1, $variation_id, array(), $params);


            if (false !== $data) {
                $or_price = 0;
                $regular_price = get_post_meta($params['post'], '_regular_price', true);
                $sale_price = get_post_meta($params['post'], '_sale_price', true);
                if (isset($regular_price) && !empty($regular_price)) {
                    if (isset($sale_price) && !empty($sale_price) && $sale_price < $regular_price):

                        $or_price = $sale_price;
                else:
                    $or_price = $regular_price;
                endif;
            }

            global $woocommerce;
            WC()->session->set($data . 'price', $or_price);
            WC()->session->set_customer_session_cookie(true);
            $response['response'] = 'success';
            $response['url'] = $woocommerce->cart->get_checkout_url();

            echo json_encode($response);
            die;
        }
        $response['response'] = 'error';
        $response['message'] = 'Something Went Worng';
        echo json_decode($response);
        die;
    }

    $response['response'] = 'error';
    $response['message'] = 'Invalid Request';
    echo json_encode($response);
    die;
}

function km_get_lens_type()
{

    if ('POST' !== strtoupper($_SERVER['REQUEST_METHOD']))
        return;

    if (check_ajax_referer('kmcl_ajax_nonce_generate', false, false) && isset($_REQUEST['category'])) {
        global $wpdb;

        $index = $this->get_lens_index($_REQUEST['right'], $_REQUEST['left']);

        $join_sql = '';

        @session_start();
        if ($index) {
            $_SESSION['lens_index'] = $index;
        } else {
            unset($_SESSION['lens_index']);
        }

        if (is_array($index)) {
            $index = implode(",", $index);
            $join_sql = " AND li.term_taxonomy_id IN ({$index}) ";
        } elseif ($index) {
            $join_sql = " AND li.term_taxonomy_id = {$index} ";
        }
        if($_REQUEST['category'] == 'skip-category'){							
        }else{							
            $sql = "
            SELECT DISTINCT  tr.term_taxonomy_id
            FROM  {$wpdb->prefix}term_relationships as tr
            INNER JOIN {$wpdb->prefix}term_relationships as jt
            ON tr.object_id = jt.object_id

            WHERE tr.object_id
            IN (
            SELECT m.object_id
            FROM {$wpdb->prefix}term_relationships as m
            INNER JOIN {$wpdb->prefix}term_relationships as li
            ON m.object_id = li.object_id
            WHERE m.term_taxonomy_id ={$_REQUEST['category']} {$join_sql}
            ) AND jt.term_taxonomy_id
            IN (
            SELECT tt.term_id
            FROM {$wpdb->prefix}term_taxonomy as tt
            WHERE tt.taxonomy='lenses-types'
        ) AND tr.term_taxonomy_id != {$_REQUEST['category']}";

        
        $results = $wpdb->get_col($sql);

       // if ($results) {
            $taxonomies = get_categories(array('include' => implode(",", $results), 'order' => 'DESC', 'taxonomy' => 'lenses-types', 'style' => false, 'echo' => false));
            kmcl_get_template('single-product/new-template/taxonomies.php', array('taxonomies' => $taxonomies));
            die;
      //  }					
    }

}

exit;
}


function km_set_locations()
{

    if ('POST' !== strtoupper($_SERVER['REQUEST_METHOD']))
        return;

    if (check_ajax_referer('kmcl_ajax_nonce_generate', false, false) && isset($_REQUEST['lat'])) {
        global $wpdb;

        @session_start();
        $_SESSION['lat'] = $_REQUEST['lat'];
        $_SESSION['lang'] = $_REQUEST['lang'];
    }

}


function km_get_lens_category()
{
    if ('POST' !== strtoupper($_SERVER['REQUEST_METHOD']))
        return;

    if (check_ajax_referer('kmcl_ajax_nonce_generate', false, false) && isset($_REQUEST['category'])) {
        global $wpdb;
        $sql = "
        SELECT DISTINCT  tr.term_taxonomy_id
        FROM  {$wpdb->prefix}term_relationships as tr
        INNER JOIN {$wpdb->prefix}term_relationships as jt
        ON tr.object_id = jt.object_id
        WHERE tr.object_id
        IN (
        SELECT m.object_id
        FROM {$wpdb->prefix}term_relationships as m
        WHERE m.term_taxonomy_id ={$_REQUEST['left']}
        ) AND jt.term_taxonomy_id
        IN (
        SELECT tt.term_id
        FROM {$wpdb->prefix}term_taxonomy as tt
        WHERE tt.taxonomy='lenses-types'
        ) AND jt.term_taxonomy_id
        IN (
        SELECT ld.term_id
        FROM {$wpdb->prefix}term_taxonomy as ld
        WHERE ld.taxonomy='lenses-category'
    ) AND tr.term_taxonomy_id != {$_REQUEST['category']}";

    $results = $wpdb->get_col($sql);

    if ($results) {
        $taxonomies = get_categories(array('include' => implode(",", $results), 'hide_empty' => false, 'order' => 'DESC', 'taxonomy' => 'lenses-types', 'style' => false, 'echo' => false));
        kmcl_get_template('single-product/new-template/taxonomies.php', array('taxonomies' => $taxonomies));
        die;
    }

}

exit;
}

function km_get_custom_lens()
{
    if ('POST' !== strtoupper($_SERVER['REQUEST_METHOD']))
        return;

    if (check_ajax_referer('kmcl_ajax_nonce_generate', false, false) && isset($_REQUEST['category'])) {
        @session_start();
        $meta_a = '';
        if (isset($_SESSION['lens_index'])) {
            $meta_a =
            array(
                'taxonomy' => 'lenses-index',
                'field' => 'term_id',
                'terms' => $_SESSION['lens_index'],
            );

        }
        $args = array(
            'post_type' => 'cm-lenses',
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'lenses-category',
                    'field' => 'term_id',
                    'terms' => array($_REQUEST['category']),
                ),
                array(
                    'taxonomy' => 'lenses-types',
                    'field' => 'term_id',
                    'terms' => array($_REQUEST['type']),
                ), $meta_a
            )
        );
        $post = new WP_Query($args);
        ?>

        <?php

        if ($post->have_posts()) {
            while ($post->have_posts()):
                $post->the_post();

                $features = array(
                    'anti-reflect' => 'Anti Reflection',
                    'uv-protective' => 'Uv Protective',
                    'scratch-resist' => 'Scratch Resistance'
                );
                $lens_feature = get_post_meta(get_the_ID(), '_lens_feature', true);
                ?>
                <div class="radio-button-cont">
                    <div class="radio-text">
                        <h5><?php the_title(); ?></h5>
                        <p>
                            <?php the_content(); ?>
                            <?php $echocontent = array(); ?>
                            <?php foreach ($features as $key => $feature): ?>
                                <?php if (in_array($key, $lens_feature)) : ?>
                                    <?php $echocontent[] = $feature; ?>
                                <?php endif; ?>

                            <?php endforeach; ?>
                            <?php echo implode(",", $echocontent) ?>


                        </p>

                    </div>
                    <?php

                    $regular_price = get_post_meta(get_the_ID(), '_regular_price', true);
                    $sale_price = get_post_meta(get_the_ID(), '_sale_price', true);
                    if (isset($regular_price) && !empty($regular_price)) {
                        if (isset($sale_price) && !empty($sale_price) && $sale_price < $regular_price): ?>
                            <span class="price strike">$<?php echo $regular_price; ?> </span>
                        <span class="price">$<?php echo $sale_price; ?> </span>
                        <?php else: ?>
                            <span class="price">$<?php echo $regular_price; ?> </span>
                        <?php endif; ?>
                        <?php
                    }
                    ?>


                    <input class="" type="radio" id="<?php the_ID(); ?>"
                    name="post" value="<?php the_ID(); ?>">
                    <label for="<?php the_ID(); ?>"></label>

                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        }

    }

    exit;
}
}