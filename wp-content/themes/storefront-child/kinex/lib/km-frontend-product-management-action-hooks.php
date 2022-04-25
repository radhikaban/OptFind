<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/7/2017
 * Time: 9:27 AM
 */


/**
 * Product Management Template
 */

add_action('init', 'km_remove_stroefornt_hoook');
function km_remove_stroefornt_hoook()
{
    global $WCMp;

    global $product;
    remove_action('storefront_header', 'storefront_social_icons', 10);
    remove_action('storefront_header', 'storefront_product_search', 40);
    remove_action('storefront_header', 'storefront_header_cart', 60);
    remove_action('storefront_header', 'storefront_secondary_navigation', 30);
    remove_action('storefront_content_top', 'woocommerce_breadcrumb', 10);




    remove_action('woocommerce_single_variation','woocommerce_single_variation_add_to_cart_button',20);




    remove_action('wcmp_vendor_dashboard_header', array($WCMp->vendor_hooks, 'wcmp_vendor_dashboard_header'));

    remove_action( 'storefront_header', 'storefront_primary_navigation',               50 );
    add_action( 'storefront_header', 'km_storefront_primary_navigation',               50 );

    add_action('storefront_header', 'custom_search', 40);
    add_action('storefront_header', 'storefront_header_cart', 40);
    add_action('storefront_header_custom', 'storefront_secondary_navigation', 30);
}
function km_storefront_primary_navigation(){
    ?>
    <nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_html_e( 'Primary Navigation', 'storefront' ); ?>">
        <button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span><?php echo esc_attr( apply_filters( 'storefront_menu_toggle_text', __( 'Menu', 'storefront' ) ) ); ?></span></button>
        <?php
        wp_nav_menu(
            array(
                'theme_location'	=> 'primary',
                'container_class'	=> 'primary-navigation',
            )
        );

        wp_nav_menu(
            array(
                'theme_location'	=> 'primary',
                'container_class'	=> 'handheld-navigation',
            )
        );
        ?>
    </nav><!-- #site-navigation -->
    <?php
}

function custom_search()
{
    // wc_get_template('header/searchform.php');
}


add_action('wcmp_vendor_dashboard_seller-product-manager_endpoint', 'wcmp_vendor_dashboard_seller_product_manager_endpoint');
function wcmp_vendor_dashboard_seller_product_manager_endpoint()
{
    global $WCMp, $wp, $wp_rewrite;


    $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
    $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
    $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
    wp_enqueue_script('wcmp_profile_edit_js', $frontend_script_path . '/profile_edit' . $suffix . '.js', array('jquery'), $WCMp->version, true);
    $vendor = get_wcmp_vendor(get_current_user_id());
    $WCMp->library->load_upload_lib();
    $WCMp->template->get_template('vendor-dashboard/seller-product-manager/seller-product-manager.php', array('vendor' => $vendor));
}

add_action('wcmp_vendor_dashboard_vendor-deal_endpoint', 'wcmp_vendor_dashboard_vendor_deal_endpoint');
function wcmp_vendor_dashboard_vendor_deal_endpoint()
{
    global $WCMp, $wp, $wp_rewrite;

    $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
    $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
    $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
    wp_enqueue_script('wcmp_profile_edit_js', $frontend_script_path . '/profile_edit' . $suffix . '.js', array('jquery'), $WCMp->version, true);
    $vendor = get_wcmp_vendor(get_current_user_id());
    $WCMp->library->load_upload_lib();
    $WCMp->template->get_template('product_deals/product_deals.php', array('vendor' => $vendor));
}


/**
 *  Add Product Management Template
 */

add_action('wcmp_vendor_dashboard_seller-add-product-manager_endpoint', 'wcmp_vendor_dashboard_seller_add_product_manager_endpoint');
function wcmp_vendor_dashboard_seller_add_product_manager_endpoint()
{
    global $WCMp, $wp, $wp_rewrite;
    $product_detail = $wp->query_vars['seller-add-product-manager'];


    if (!empty($product_detail)) {
        $frontend_script_path = $WCMp->plugin_url . 'assets/frontend/js/';
        $frontend_script_path = str_replace(array('http:', 'https:'), '', $frontend_script_path);
        $suffix = defined('WCMP_SCRIPT_DEBUG') && WCMP_SCRIPT_DEBUG ? '' : '.min';
        wp_enqueue_script('wcmp_profile_edit_js', $frontend_script_path . '/profile_edit' . $suffix . '.js', array('jquery'), $WCMp->version, true);
        $WCMp->library->load_upload_lib();

        $WCMp->template->get_template('vendor-dashboard/seller-product-manager/seller-product-manager-details.php', array('product_detail' => $product_detail));
    } else {
        $vendor = get_wcmp_vendor(get_current_user_id());
        $user_array = $WCMp->user->get_vendor_fields($vendor->id);
        $WCMp->template->get_template('vendor-dashboard/seller-product-manager/seller-add-product-manager.php', $user_array);
    }


}

/**
 * Save product
 */

add_action('template_redirect', 'km_save_vendor_dashboard_data', 60);

function km_save_vendor_dashboard_data()
{
    global $WCMp, $kmi;
    $vendor = get_wcmp_vendor(get_current_user_id());

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        switch ($WCMp->endpoints->get_current_endpoint()) {
            case 'seller-add-product-manager':
                $error = $kmi->product_management->save_product($vendor->id, $_POST);
                if (empty($error)) {
                    wc_add_notice(__('Product Saved', 'dc-woocommerce-multi-vendor'), 'success');
                } else {
                    wc_add_notice($error, 'error');
                }
                break;
            case 'seller-product-manager':
                if (isset($_POST['product-slug'])) {
                    $error = $kmi->product_management->delete_product($vendor->id, $_POST);
                } else {
                    $error = $kmi->product_management->save_product($vendor->id, $_POST);
                }

                if (empty($error)) {
                    wc_add_notice(__('Product deleted successfully', 'dc-woocommerce-multi-vendor'), 'success');
                } else {
                    wc_add_notice($error, 'error');
                }
                break;
            default :
                break;
        }
    }
}

/**
 * Header For Product Management
 */

add_action('wcmp_vendor_dashboard_header', 'km_wcmp_vendor_dashboard_header');

function km_wcmp_vendor_dashboard_header()
{
    global $WCMp, $wp;


    switch ($WCMp->endpoints->get_current_endpoint()) {
        case 'shop-front':
            echo '<ul>';
            echo '<li>' . __('Store Settings ', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '<li class="next"> < </li>';
            echo '<li>' . __('Edit Store Front', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
           // echo '<button class="wcmp_ass_btn edit_shop_settings">' . __('Edit', 'dc-woocommerce-multi-vendor') . '</button>';
            break;
        case 'vendor-policies':
            echo '<ul>';
            echo '<li>' . __('Store Settings ', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '<li class="next"> < </li>';
            echo '<li>' . __('Edit Policies', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
          //  echo '<button class="wcmp_ass_btn edit_policy">' . __('Edit', 'dc-woocommerce-multi-vendor') . '</button>';
            break;
        case 'vendor-billing':
            echo '<ul>';
            echo '<li>' . __('Store Settings ', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '<li class="next"> < </li>';
            echo '<li>' . __('Edit Billing', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
           // echo '<button class="wcmp_ass_btn edit_billing">' . __('Edit', 'dc-woocommerce-multi-vendor') . '</button>';
            break;
        case 'vendor-shipping':
            echo '<ul>';
            echo '<li>' . __('Store Settings ', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '<li class="next"> < </li>';
            echo '<li>' . __('Edit Shipping', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
          //  echo '<button class="wcmp_ass_btn edit_shipping">' . __('Edit', 'dc-woocommerce-multi-vendor') . '</button>';
            break;
        case 'vendor-report':
            echo '<ul>';
            echo '<li>' . __('Stats & Reports', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '<li class="next"> > </li>';
            echo '<li>' . __('Overview', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
            break;
        case 'vendor-orders':
            echo '<ul>';
            echo '<li>' . __('Order &amp; Shipping', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
            break;
        case 'vendor-withdrawal':
            echo '<ul>';
            echo '<li>' . __('Payments', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '<li class="next"> > </li>';
            echo '<li>' . __('Withdrawals', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
            break;
        case 'transaction-details':
            echo '<ul>';
            echo '<li>' . __('Payments', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '<li class="next"> > </li>';
            echo '<li>' . __('History', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
            break;
        case 'vendor-knowledgebase':
            echo '<ul>';
            echo '<li>' . __('Knowledgebase', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
            break;
        case 'vendor-announcements':
            echo '<ul>';
            echo '<li>' . __('Announcements', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
            break;
        case 'seller-product-manager':
            echo '<ul>';
            echo '<li>' . __('Product Management ', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
            echo '<a href="' . get_product_page_url(apply_filters('km_seller_add_product_manager_url', 'seller-add-product-manager')) . '" class="wcmp_ass_btn">' . __('Add Product', 'dc-woocommerce-multi-vendor') . '</a>';
            break;
        case 'seller-add-product-manager':
            echo '<ul>';
            echo '<li>' . __('Product Management ', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
            echo '<a style="margin-left:20px" href="' . get_product_page_url(apply_filters('km_seller_add_product_manager_url', 'seller-product-manager')) . '" class="wcmp_ass_btn">' . __('All Products', 'dc-woocommerce-multi-vendor') . '</a>';
            break;

        case '':
            echo '<ul>';
            echo '<li>' . __('Dashboard', 'dc-woocommerce-multi-vendor') . '</li>';
            echo '</ul>';
            echo '<span>' . Date('d M Y') . '</span>';
            break;
        default:
            break;
    }
}


add_action('wp_ajax_nopriv_get_listing_names', 'ajax_listings');
add_action('wp_ajax_get_listing_names', 'ajax_listings');

function ajax_listings()
{
    global $wpdb; //get access to the WordPress database object variable

    $name = $wpdb->esc_like(stripslashes($_POST['name'])) . '%'; //escape for use in LIKE statement
    $dc_vendor_id = get_user_meta(get_current_user_id(), '_vendor_term_id', true);
    $sql = "select DISTINCT post_title, ID 
		from $wpdb->posts 
		where nd ID NOT IN ((SELECT post_id from  {$wpdb->postmeta}  where meta_key='p_parent_id')) and  post_type='product' and post_status='publish' and ID NOT IN (  select DISTINCT p.product_id from {$wpdb->term_relationships} as tr JOIN " . KM_MAP_TABLE . " as p on FIND_IN_SET(tr.object_id, p.product_ids ) > 0 where tr.term_taxonomy_id={$dc_vendor_id} and p.is_active=1)  GROUP BY post_title order by post_title";


    $sql = $wpdb->prepare($sql, $name);

    $results = $wpdb->get_results($sql);

    //copy the business titles to a simple array
    $titles = array();
    $i = 0;
    foreach ($results as $r) {
        $titles[$i]['ID'] = $r->ID;
        $titles[$i]['value'] = addslashes($r->post_title);
        $titles[$i]['label'] = addslashes($r->post_title);
        $i++;
    }


    echo json_encode($titles); //encode into JSON format and output

    die(); //stop "0" from being output
}

add_action('wp_ajax_nopriv_get_product_template', 'km_get_product_template');
add_action('wp_ajax_get_product_template', 'km_get_product_template');

/**
 *
 */
function km_get_product_template()
{
    global $wpdb; //get access to the WordPress database object variable
    global $WCMp, $wp;
    $message = array();

    if (isset($_POST['ID']) && !empty($_POST['ID']) && isset($_REQUEST['__nonce']) && wp_verify_nonce($_POST['__nonce'], 'km_add_own_product_users')) {


        $wc_adp = new WC_Admin_Duplicate_Product;
        $product = wc_get_product($_POST['ID']);


        if (false === $product) {
            $message['response'] = 'failed';
            $message['msg'] = sprintf(__('Product creation failed, could not find original product: %s', 'woocommerce'), $_POST['ID']);
            wp_die();
            echo json_encode($message);
        }
        $pterms = wc_get_object_terms($product->get_id(), 'pwb-brand');


        $duplicate = $wc_adp->product_duplicate($product);
        $psetterm = array();
        foreach ($pterms as $pterm) {
            $psetterm[] = $pterm->term_id;
        }


        wp_set_object_terms($duplicate->get_id(), $psetterm, 'pwb-brand');

        do_action('woocommerce_product_duplicate', $duplicate, $product);

        $dc_vendor_id = get_user_meta(get_current_user_id(), '_vendor_term_id', true);


        $term = get_term($dc_vendor_id, 'dc_vendor_shop');


        wp_set_object_terms($duplicate->get_id(),$term->term_id, 'dc_vendor_shop');
        $my_post = array(
            'ID' => $duplicate->get_id(),
            'post_status' => 'publish',
            'post_title' => trim(str_replace('(Copy)', '', $duplicate->get_title())),

        );
        wp_update_post($my_post);

        update_data_to_products_map_table($_POST['ID'], $duplicate->get_id());
        update_post_meta($duplicate->get_id(), 'p_parent_id', $_POST['ID']);


        $message['response'] = 'success';
        $message['msg'] = "Product owned";
        $message['link'] = '<a title="View" href="' . esc_url(wcmp_get_vendor_dashboard_endpoint_url(apply_filters('km_seller_add_product_manager_url', 'seller-add-product-manager'), $duplicate->get_id())) . '">Edit Product</a>&nbsp';
        echo json_encode($message);
        wp_die();

    } else {
        $message['response'] = 'failed';
        $message['msg'] = "Something went wrong!";
        echo json_encode($message);
        wp_die();
    }
    $message['response'] = 'failed';
    $message['msg'] = "Something went wrong!";
    echo json_encode($message);
    wp_die();

}

/**
 * Update date map table for vendor
 * @param $post_id
 */
function update_data_to_products_map_table($parent_id, $post_id)
{
    global $WCMp, $wpdb;
    $post = get_post($post_id);
    if ($post->post_type == 'product') {
        $results = $wpdb->get_results("select * from " . KM_MAP_TABLE . " where product_id = '{$parent_id}' ");
        if (is_array($results) && (count($results) > 0)) {
            $id_of_similar = $results[0]->ID;
            $product_ids = $results[0]->product_ids;
            $product_ids_arr = explode(',', $product_ids);
            if (is_array($product_ids_arr) && in_array($post_id, $product_ids_arr)) {

            } else {
                $product_ids = $product_ids . ',' . $post->ID;
                $wpdb->query("update " . KM_MAP_TABLE . " set product_ids = '{$product_ids}' where ID = {$id_of_similar}");
            }
        } else {
            $wpdb->query("insert into " . KM_MAP_TABLE . " set product_id='{$parent_id}', product_ids = '{$post->ID}' ");
        }
    }
}

//add_action('wp_trash_post','km_delete_post_meta',1,1);
add_action( 'before_delete_post', 'km_delete_post_meta' );
function km_delete_post_meta( $postid ){

    // We check if the global post type isn't ours and just return
    global $post_type,$wpdb;
    if ( $post_type != 'product' ) return;


    $terms = wp_get_post_terms( $postid, 'dc_vendor_shop' );

    debug($terms,false);

    echo $sql = "select  p.* from {$wpdb->term_relationships} as tr JOIN " . KM_MAP_TABLE . " as p on FIND_IN_SET(tr.object_id, p.product_ids ) > 0 where tr.term_taxonomy_id={$terms[0]->term_taxonomy_id} and tr.object_id={$postid} and p.is_active=1";
    $product_list_arr = $wpdb->get_results($sql);

    if(count($product_list_arr) > 0){
        foreach ($product_list_arr as $product_map){
                $product_ids = $product_map->product_ids;
                $product_ids = explode(",",$product_ids);

                if (is_array($product_ids) && in_array($postid, $product_ids)) {

                $pos = array_search($postid, $product_ids);
                unset($product_ids[$pos]);
                $product_ids = implode(",",$product_ids);
                $wpdb->query("update " . KM_MAP_TABLE . " set product_ids = '{$product_ids}' where ID = {$product_map->ID}");
            }

        }


    }
    // My custom stuff for deleting my custom post type here
}


add_action('wp_ajax_km_get_product_list', 'km_get_product_list');

function km_get_product_list()
{

    global $WCMp, $wpdb;

    $dc_vendor_id = get_user_meta(get_current_user_id(), '_vendor_term_id', true);
    $sql = "SELECT post_id from  {$wpdb->postmeta}  where meta_key='p_parent_id'";
    $sql2 = "select DISTINCT p.product_id from {$wpdb->term_relationships} as tr JOIN " . KM_MAP_TABLE . " as p on FIND_IN_SET(tr.object_id, p.product_ids ) > 0 where tr.term_taxonomy_id={$dc_vendor_id} and p.is_active=1";
    $result = $wpdb->get_col($sql);
    $result2 = $wpdb->get_col($sql2);
    $user_p_ids = array_merge($result, $result2);


    if (!isset($_POST['km_get_products_list']) || !wp_verify_nonce($_POST['km_get_products_list'], 'km_get_products_list')) {
        print 'Sorry, your nonce did not verify.';
        exit;
    } else {
        $attributes_terms = $tax_query = array();
        $attributes = km_get_product_attributes();
        $taxonomies = array();

        if (!empty($attributes)) {
            foreach ($attributes as $k => $v) {
                $terms = get_terms(array($k),  array('orderby' => 'name', 'hide_empty' => false, 'order' => 'ASC', 'fields' => 'id=>slug'));
                if ($terms) {
                    foreach ($terms as $term_id => $term_slug) {
                        $attributes_terms[$k][$term_id] = $term_slug;
                    }
                }
                unset($terms);
            }
        }

        unset($attributes);


        if (!empty($_POST['data'])) {
            foreach ($_POST['data'] as $k => $t) {
                if (!isset($taxonomies[$k])) {
                    $taxonomies[$k] = array();
                }
                foreach ($t as $t_val) {
                    $taxonomies[$k][] = @$attributes_terms[$k][$t_val];
                }
                //$taxonomies_operator[$t[0]] = $t[2];
            }
        }
        unset($attributes_terms);

        if (!empty($taxonomies)) {
            $tax_query['relation'] = 'AND';
            if ($taxonomies) {
                foreach ($taxonomies as $k => $v) {
                    $op = 'IN';
                    $tax_query[] = array(
                        'taxonomy' => $k,
                        'field' => 'slug',
                        'terms' => $v,
                        'operator' => $op
                    );
                }
            }
        }




        if (isset($_POST['product_cat'])) {

            $tax_query['relation'] = 'AND';
            $ter = array();
            foreach ($_POST['product_cat'] as $product_cat) {
                $ter[] = $product_cat;
            }

            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $ter,
                'operator' => 'IN'
            );
        }

        if (isset($_POST['pwb-brand'])) {
            $tax_query['relation'] = 'AND';
            $ter = array();
            foreach ($_POST['pwb-brand'] as $product_cat) {
                $ter[] = $product_cat;
            }

            $tax_query[] = array(
                'taxonomy' => 'pwb-brand',
                'field' => 'id',
                'terms' => $ter,
                'operator' => 'IN'
            );
        }

        if (isset($_POST['pagenumber']) && !empty($_POST['pagenumber'])) {
            $args['paged'] = $_POST['pagenumber'];
        } else {
            $args['paged'] = 1;
        }
        if (isset($_POST['postperpage']) && !empty($_POST['postperpage']))  {
            $args['posts_per_page'] = $_POST['postperpage'];
            //  $args['paged'] =1;
        } else {
            $args['posts_per_page'] = KM_POST_PER_PAGE;
        }


        $args['tax_query'] = $tax_query;
        $args['post_type'] = 'product';


        if (!empty($_POST['orderby'])) {
            km_parse_order_by($args);
        }


        if(count($tax_query) < 1){
            unset($args['tax_query']);
        }

        $args['post__not_in'] = $user_p_ids;


        if (isset($_REQUEST['load_more']) && !empty($_REQUEST['load_more'])) {
            $WCMp->template->get_template('single-product.php', array('args' => $args));
        } else {
            $WCMp->template->get_template('archive-products.php', array('args' => $args));
        }


        exit;
    }
}


add_action('km_woocommerce_after_shop_loop_item', 'km_add_user_products_button', 15, 1);
function km_add_user_products_button($args)
{
    global $product, $post;
    if (!is_tax('dc_vendor_shop') && !isset($args['is_taxonomy'])) {
        $nonce = wp_create_nonce('km_add_own_product_users');
        echo "<div class='add_product_button_wrapper'><span class='km_msg'></span><a href='javascript:void(0)' class='km_add_own_product btn btn-primary' data-id='" . get_the_ID() . "' data-nonce='" . $nonce . "'>Add Product </a></div>";
    }

}

//add_filter( 'loop_shop_per_page', 'new_loop_shop_per_page', 20 );

function new_loop_shop_per_page($cols)
{
    // $cols contains the current number of products per page based on the value stored on Options -> Reading
    // Return the number of products you wanna show per page.
    $cols = 3;
    return $cols;
}

add_action('init', 'km_search_template');
function km_search_template()
{
    global $wp_query;
    if (isset($_REQUEST['kms']) && !empty($_REQUEST['kms'])) {

        $newtax_query = array();

        $attributes = km_get_product_attributes();
        $attributes_terms = array();
        if (!empty($attributes)) {
            foreach ($attributes as $k => $v) {
                $terms = get_terms(array($k),  array('orderby' => 'name', 'hide_empty' => false, 'order' => 'ASC', 'fields' => 'id=>slug', 'name__like' => $_REQUEST['kms']));
                if ($terms) {
                    foreach ($terms as $term_id => $term_slug) {
                        $attributes_terms[$k][$term_id] = $term_slug;
                    }
                }
                unset($terms);
            }
        }

        foreach ($attributes_terms as $key => $attributes_term):
            $newtax_query[] = array(
                'taxonomy' => $key,
                'terms' => $attributes_term
            );
        endforeach;

        $cats = array('product_cat', 'pwb-brand');
        foreach ($cats as $cat) {
            $terms = get_terms($cat,  array('orderby' => 'name', 'hide_empty' => false, 'order' => 'ASC', 'fields' => 'id=>slug', 'name__like' => $_REQUEST['kms']));
            $attributes_terms = array();
            if ($terms) {
                foreach ($terms as $term_id => $term_slug) {
                    $attributes_terms[$cat][$term_id] = $term_slug;
                }
            }
            unset($terms);
            foreach ($attributes_terms as $key => $attributes_term):
                $newtax_query[] = array(
                    'taxonomy' => $key,
                    'terms' => $attributes_term

                );
            endforeach;

        }


        if (count($newtax_query) > 0) {
            $tax_query = $newtax_query;

            $url = "filter/";
            foreach ($tax_query as $tax) {
                $ter = implode("-", $tax['terms']);
                $url .= $tax['taxonomy'] . "/" . $ter . "/";
            }

            $site_url = get_permalink(wc_get_page_id('shop')) . $url;
            wp_redirect($site_url);
            die;

        }else{
            $site_url = site_url('/') ;

            $url = add_query_arg('s',$_REQUEST['kms'],$site_url);
            wp_redirect($url);
            die;

        }

    }
}