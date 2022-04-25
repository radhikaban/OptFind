<?php

/**

 * Created by PhpStorm.

 * User: Sarab Sodhi

 * Date: 9/6/2017

 * Time: 11:30 AM

 */

/***

 * Debug Function

 * @param $data

 * @param bool $exit

 */

function debug($data, $exit = true)

{

    if (KM_DEBUG) {

        echo "<pre>";

        print_r($data);

        echo "</pre>";

        if ($exit) {

            die;

        }

    }

}





add_filter('woocommerce_loop_add_to_cart_link','km_tm_filter_callback_link');



add_action('admin_init','admin_product_detail');
function admin_product_detail() {
	add_meta_box( 'display_product_admin_detail',
		'Product Information',
		'display_product_admin_detail',
		'product', 'normal', 'high'
	);
}
function display_product_admin_detail() {
	global $post;
	$post_id = get_the_ID();
	$eye_size = get_post_meta( $post_id, 'eye_size', true);
	$bridge = get_post_meta( $post_id, 'bridge', true);
	$temple_lenght= get_post_meta( $post_id, 'temple_lenght', true);
?>
	<div><label>Eye size:</label></div>
	<div><input type="text" name="eye_size" value="<?php echo $eye_size; ?>"/></div>
    <div><label>Bridge:</label></div>
    <div><input type="text" name="bridge" value="<?php echo $bridge; ?>"/></div>
	<div><label>Temple length:</label></div>
    <div><input type="text" name="temple_lenght" value="<?php echo $temple_lenght; ?>"/></div>
	
<?php 
}
add_action( 'save_post', 'save_more_admin_product_detail', 10, 3 );
function save_more_admin_product_detail($post_id, $post,$update )
{
	global $post;
	if($post->post_type == 'product') {
	if($_POST['eye_size']){
		update_post_meta($post_id, 'eye_size', $_POST['eye_size']);
	}
	if($_POST['bridge']){
		update_post_meta($post_id, 'bridge', $_POST['bridge']);
	}
	if($_POST['temple_lenght']){
		update_post_meta($post_id, 'temple_lenght', $_POST['temple_lenght']);
	}
	
	}
}

add_filter( 'woocommerce_product_tabs', 'custom_product_tabs' );
function custom_product_tabs( $tabs ) {
    global $post;
	
    $eye_size = get_post_meta( $post->ID, 'eye_size', true );
    $bridge    = get_post_meta( $post->ID, 'bridge', true );
    $temple_lenght    = get_post_meta( $post->ID, 'temple_lenght', true );

  
        $tabs['additional_information'] = array(
            'title'    => __( 'Additional information', 'woocommerce' ),
            'priority' => 45,
            'callback' => 'custom_product_additional_information_tab'
        );
		return $tabs;

}

function custom_product_additional_information_tab(){
	global $post;
	
	
	/* Get variation attribute based on product ID */
		$product = new WC_Product_Variable( $post->ID );
		$attributes = $product->get_attributes();

		
		 foreach($attributes as $key =>  $value){
                $attribute_name = preg_replace('/pa_/', '', $key);
				$attribute_name = wc_get_product_terms($post->ID , $key); // GET ATTRIBUTE NAME
				
			
				if(!empty($attribute_name)){
                 //foreach($attribute_name as $name) {
                  
                     echo '<span>'.ucwords($attribute_name1 = preg_replace('/pa_/', '', $key)).'</span><p>'.$attribute_name->name.'</p>';
                 //}
				}
         }
		
		

		/*Get attributes from loop*/
		
	$eye_size = get_post_meta( $post->ID, 'eye_size', true );
    $bridge    = get_post_meta( $post->ID, 'bridge', true );
    $temple_lenght    = get_post_meta( $post->ID, 'temple_lenght', true );
	
	if($eye_size){
		
		echo '<span>Eye Size</span><p>'.$eye_size.'</p>';
	}
	if($bridge){
		echo '<span>Bridge</span><p>'.$bridge.'</p>';
	}
	if($temple_lenght){
		echo '<span>Temple Lenght</span><p>'.$temple_lenght.'</p>';
	}
	
}



function km_tm_filter_callback_link($link){

    $new_link ="<span class='km_add_to_cart'>";



    $new_link .= $link;

    $new_link .="</span>";

    return $new_link;

}



add_action('woocommerce_checkout_create_order_shipping_item','remove_vendor_meta',15,4);

function remove_vendor_meta($item, $package_key, $package, $order){

    $item->delete_meta_data( 'vendor_id' );

}

add_filter('woocommerce_shipping_package_name','km_woocommerce_shipping_package_name', 100, 3);



function km_woocommerce_shipping_package_name($package_name, $item_id, $package) {



    if ($item_id && $item_id != 0) {



        $product = wc_get_product($item_id);

        return  __("<a href='".get_permalink($item_id)."'>".get_the_title($item_id)." ( #".$item_id." ) Shipping</a>", 'dc-woocommerce-multi-vendor');

    }

    return  __(' Shipping', 'dc-woocommerce-multi-vendor');



}





 function km_split_shipping_packages($packages=array()) {

    // Reset all packages

    $packages = array();

    $split_packages = array();



    foreach (WC()->cart->get_cart() as $item) {





        if ($item['data']->needs_shipping()) {

            $product_id = $item['product_id'];

            if(isset($item['variation_id']) && $item['variation_id'] != 0){

                $product_id = $item['variation_id'];

            }

            $split_packages[$product_id][] = $item;

        }

    }



    foreach ($split_packages as $vendor_id => $split_package) {

        $packages[$vendor_id] = array(

            'contents' => $split_package,

            'contents_cost' => array_sum(wp_list_pluck($split_package, 'line_total')),

            'applied_coupons' => WC()->cart->get_applied_coupons(),

            'user' => array(

                'ID' => get_current_user_id(),

            ),

            'destination' => array(

                'country' => WC()->customer->get_shipping_country(),

                'state' => WC()->customer->get_shipping_state(),

                'postcode' => WC()->customer->get_shipping_postcode(),

                'city' => WC()->customer->get_shipping_city(),

                'address' => WC()->customer->get_shipping_address(),

                'address_2' => WC()->customer->get_shipping_address_2()

            )

        );

    }

    return $packages;

}



add_filter('wcmp_split_shipping_packages','km_split_shipping_packages');

function custom_menu_page_removing()

{

    remove_menu_page("edit.php?post_type=wcmp_vendorrequest");

}









add_action('admin_menu', 'custom_menu_page_removing');





add_filter('tm_woocommerce_list_product_image', 'km_wishlist_image');

function km_wishlist_image()

{

    ob_start();

    global $post;

    ?>





    <div class="tm-woocomerce-list__product-image">

        <a href="<?php echo get_permalink($post->ID) ?>">

            <?php





            if (has_post_thumbnail($post->ID)) {

                echo get_the_post_thumbnail($post->ID);

            } else {

                echo sprintf('<img src="%s" alt="%s" class="wp-post-image" />', esc_url(wc_placeholder_img_src()), esc_html__('Awaiting product image', 'woocommerce'));



            }

            ?>

        </a>

    </div>

    <?php



    $image = ob_get_contents();



    ob_clean();

    return $image;





}



function km_show_custom()

{



}





add_action('personal_options_update', 'km_save_vendor_data');

add_action('edit_user_profile_update',  'km_save_vendor_data');



function km_save_vendor_data($user_id){



    // only saves if the current user can edit user profiles

    global $WCMp;

    if (!current_user_can('edit_user', $user_id)) {

        return false;

    }

    $errors = new WP_Error();

    $fields = $WCMp->user->get_vendor_fields($user_id);

    $vendor = get_wcmp_vendor($user_id);

    if ($vendor) {



        $street_address_fields = wc_clean($_POST['vendor_address_1']);

        $city_fields = wc_clean($_POST['vendor_city']);

        $state_fields = wc_clean($_POST['vendor_state']);

        $country_fields = wc_clean($_POST['vendor_country']);

        $postal_fields = wc_clean($_POST['vendor_postcode']);



        $address = '';



        if (!empty($street_address_fields)) {

            $address .= $street_address_fields . ", ";

        }

        if (!empty($city_fields)) {

            $address .= $city_fields . ", ";

        }

        if (!empty($state_fields)) {

            $address .= $state_fields . ", ";

        }

        if (!empty($country_fields)) {

            $address .= $country_fields . ", ";

        }

        if (!empty($postal_fields)) {

            if (!empty($address)) {

                $address = trim($address, ", ");

            }



            $address .= $postal_fields . ", ";

        }



        $geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false');

        $geo = json_decode($geo, true); // Convert the JSON to an array



        if (isset($geo['status']) && ($geo['status'] == 'OK')) {

             $latitude = $geo['results'][0]['geometry']['location']['lat']; // Latitude

             $longitude = $geo['results'][0]['geometry']['location']['lng']; // Longitude



            insert_vendor_lat($user_id,$latitude,$longitude);



        }

    }

}









add_action('before_wcmp_vendor_dashboard',  'km_save_vendor_dashboard_data_lat');



function km_save_vendor_dashboard_data_lat() {

    global $WCMp;

    $vendor = get_wcmp_vendor(get_current_vendor_id());

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {

        switch ($WCMp->endpoints->get_current_endpoint()) {

            case 'shop-front':

            case 'vendor-policies':

            case 'vendor-billing':

                $error = km_save_store_settings($vendor->id, $_POST);



                break;

            default :

                break;

        }

    }

}



function km_save_store_settings($user_id,$post){

    global $WCMp;

    if (!current_user_can('edit_user', $user_id)) {

        return false;

    }



    $vendor = get_wcmp_vendor($user_id);

    if ($vendor) {



        $street_address_fields = wc_clean($post['vendor_address_1']);

        $city_fields = wc_clean($post['vendor_city']);

        $state_fields = wc_clean($_POST['vendor_state']);

        $country_fields = wc_clean($post['vendor_country']);

        $postal_fields = wc_clean($post['vendor_postcode']);



        $address = '';



        if (!empty($street_address_fields)) {

            $address .= $street_address_fields . ", ";

        }

        if (!empty($city_fields)) {

            $address .= $city_fields . ", ";

        }

        if (!empty($state_fields)) {

            $address .= $state_fields . ", ";

        }

        if (!empty($country_fields)) {

            $address .= $country_fields . ", ";

        }

        if (!empty($postal_fields)) {

            if (!empty($address)) {

                $address = trim($address, ", ");

            }



            $address .= $postal_fields . ", ";

        }



        $geo = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false');

        $geo = json_decode($geo, true); // Convert the JSON to an array



        if (isset($geo['status']) && ($geo['status'] == 'OK')) {

            $latitude = $geo['results'][0]['geometry']['location']['lat']; // Latitude

            $longitude = $geo['results'][0]['geometry']['location']['lng']; // Longitude



            insert_vendor_lat($user_id,$latitude,$longitude);



        }

    }

}





function insert_vendor_lat($user_id,$latitude,$longitude){

    global $wpdb;



    $sql = "select id  from ".KM_LATLANG_TABLE." where vendor_id = {$user_id}";

    $results = $wpdb->get_row($sql);



    if($results){

        $sql = "update ".KM_LATLANG_TABLE." set lat='{$latitude}',lang='{$longitude}' where vendor_id={$user_id}";

        $wpdb->query($sql);

    }else{

        $sql = "insert into ".KM_LATLANG_TABLE."  (vendor_id,lat,lang,is_active) values({$user_id},'{$latitude}','{$longitude}',1)";



        $wpdb->query($sql);

    }

}





add_action('init', 'km_save_customer_meta_fields', 9, 1);



function km_save_customer_meta_fields()

{



    $user_id = get_current_user_id();





    if (isset($_REQUEST['store_save'])) {



        if (isset($_FILES['vendor_fshop_i'])) {



            if (!function_exists('wp_handle_upload')) {

                require_once(ABSPATH . 'wp-admin/includes/file.php');

            }



            if (isset($_FILES['vendor_fshop_i']['name']) && !empty($_FILES['vendor_fshop_i']['name'])) {

                $uploadedfile = $_FILES['vendor_fshop_i'];



                $upload_overrides = array('test_form' => false);



                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);



                if ($movefile && !isset($movefile['error'])) {



                    update_user_meta($user_id, '_vendor_fshop', $movefile['url']);

                }





            } elseif (!isset($_REQUEST['vendor_fshop_i_image']) || empty($_REQUEST['vendor_fshop_i_image'])) {

                update_user_meta($user_id, '_vendor_fshop', '');

            }





        } elseif (!isset($_REQUEST['vendor_fshop_i_image']) || empty($_REQUEST['vendor_fshop_i_image'])) {

            update_user_meta($user_id, '_vendor_fshop', '');

        }

        if (isset($_FILES['vendor_banner_i'])) {



            if (isset($_FILES['vendor_banner_i']['name']) && !empty($_FILES['vendor_banner_i']['name'])) {

                if (!function_exists('wp_handle_upload')) {

                    require_once(ABSPATH . 'wp-admin/includes/file.php');

                }



                $uploadedfile = $_FILES['vendor_banner_i'];



                $upload_overrides = array('test_form' => false);



                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);



                if ($movefile && !isset($movefile['error'])) {



                    update_user_meta($user_id, '_vendor_banner', $movefile['url']);

                }

            } elseif (!isset($_REQUEST['vendor_banner_i_image']) || empty($_REQUEST['vendor_banner_i_image'])) {

                update_user_meta($user_id, '_vendor_banner', '');

            }

        } elseif (!isset($_REQUEST['vendor_banner_i_image']) || empty($_REQUEST['vendor_banner_i_image'])) {

            update_user_meta($user_id, '_vendor_banner', '');

        }

        if (isset($_FILES['vendor_image_i'])) {





            if (isset($_FILES['vendor_image_i']['name']) && !empty($_FILES['vendor_image_i']['name'])) {

                if (!function_exists('wp_handle_upload')) {

                    require_once(ABSPATH . 'wp-admin/includes/file.php');

                }



                $uploadedfile = $_FILES['vendor_image_i'];



                $upload_overrides = array('test_form' => false);



                $movefile = wp_handle_upload($uploadedfile, $upload_overrides);



                if ($movefile && !isset($movefile['error'])) {



                    update_user_meta($user_id, '_vendor_image', $movefile['url']);

                    update_user_meta($user_id, '_vendor_fshop', $movefile['url']);

                }

            } elseif (!isset($_REQUEST['vendor_image_i_image']) || empty($_REQUEST['vendor_image_i_image'])) {

                update_user_meta($user_id, '_vendor_image', '');

                update_user_meta($user_id, '_vendor_fshop', '');

            } else {

                update_user_meta($user_id, '_vendor_fshop', $_REQUEST['vendor_image_i_image']);

            }

        } elseif (!isset($_REQUEST['vendor_image_i_image']) || empty($_REQUEST['vendor_image_i_image'])) {



            update_user_meta($user_id, '_vendor_image', '');

            update_user_meta($user_id, '_vendor_fshop', '');

        } else {





            update_user_meta($user_id, '_vendor_fshop', $_REQUEST['vendor_image_i_image']);

        }

    }

}



/**

 * Get Product url by endpoint

 * @param $endpoint

 * @return string

 *

 */

function get_product_page_url($endpoint)

{

    return esc_url(wcmp_get_vendor_dashboard_endpoint_url($endpoint));

}





if (!function_exists('km_wcmp_get_vendor_dashboard_nav_item_css_class')) {



    function km_wcmp_get_vendor_dashboard_nav_item_css_class($endpoint, $force_active = false)

    {

        global $wp;

        $cssClass = array(

            'wcmp-venrod-dashboard-nav-link',

            'wcmp-venrod-dashboard-nav-link--' . $endpoint

        );

        $current = isset($wp->query_vars[$endpoint]);





        if ('dashboard' === $endpoint && (isset($wp->query_vars['page']) || empty($wp->query_vars))) {

            $current = true; // Dashboard is not an endpoint, so needs a custom check.

        } else if ($endpoint == 'vendor-products') {

            $current = isset($wp->query_vars['seller-product-manager']);

            if (!$current) {

                $current = isset($wp->query_vars['seller-add-product-manager']);

            }

        }

        if ($current || $force_active) {

            $cssClass[] = 'active';

        }

        $cssClass = apply_filters('wcmp_vendor_dashboard_nav_item_css_class', $cssClass, $endpoint);

        return $cssClass;

    }

}





function km_get_terms($cat = 'product_cat', $field = 'all', $is_child = true, $hide_empty = false, $parent = 0, $wrap = false, $wrap_sub = true)

{



    $args = array(

        'taxonomy' => $cat,

        'hide_empty' => $hide_empty,

        'parent' => $parent,

        'orderby' => 'name',

        'order' => 'ASC',

        'fields' => $field

    );





    $product_cat = get_terms($args);



    if (count($product_cat) > 0) {



        if ($wrap) {

            echo "<ul class='list-inline'>";

        }

        foreach ($product_cat as $parent_product_cat) {



            $name = ($cat == 'product_cat') ? $cat . "[]" : ($cat == 'pwb-brand') ? $cat . "[]" : "data[" . $cat . "][]";



            echo '<li><label for="' . $parent_product_cat->slug . '"><input name="' . $name . '" value="' . $parent_product_cat->term_id . '" class="km_product_filter" type="checkbox" id="' . $parent_product_cat->slug, '" />' . $parent_product_cat->name . '</label>';



            if ($is_child) {

                km_get_terms($cat, $field, $is_child, $hide_empty, $parent_product_cat->term_id, $wrap_sub, $wrap_sub);

            }



            echo "</li>";

        }

        if ($wrap) {

            echo "</ul>";

        }



    }

}



function km_get_product_attributes()

{

    $attribute_taxonomies = wc_get_attribute_taxonomies();

    $attributes = array();



    if ($attribute_taxonomies) {

        foreach ($attribute_taxonomies as $tax) {

            $attributes[wc_attribute_taxonomy_name($tax->attribute_name)] = $tax->attribute_label;

        }

    }

    return $attributes;

}



function km_parse_order_by($args, $post)

{

    $ordering_args = WC()->query->get_catalog_ordering_args($post['orderby'], 'ASC');

    $args['orderby'] = $ordering_args['orderby'];

    $args['order'] = $ordering_args['order'];

}



function km_get_product_search_form($echo = true)

{

    global $km_product_search_form_index;



    ob_start();



    if (empty($product_search_form_index)) {

        $km_product_search_form_index = 0;

    }





    wc_get_template('home-searchform.php', array(

        'index' => $km_product_search_form_index++,

    ));



    $form = apply_filters('km_get_product_search_form', ob_get_clean());



    if ($echo) {

        echo $form;

    } else {

        return $form;

    }

}

