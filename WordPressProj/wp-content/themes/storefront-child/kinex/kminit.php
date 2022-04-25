<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/6/2017
 * Time: 11:24 AM
 */

global $wpdb;
define('KM_DEBUG', true);
define('KM_POST_PER_PAGE', 12);

define('KM_MAP_TABLE', $wpdb->prefix . "product_map_relations");
define('KM_LATLANG_TABLE', $wpdb->prefix . "vendor_locations");
define('WCMP_HIDE_MULTIPLE_PRODUCT',true);

global $kmi;

class KM_init
{

    public $product_management;

    /**
     * KM_initialize constructor.
     */

    public function __construct()
    {

        $this->include_function_files();
        $this->include_classes();
        $this->init_instance();

        if (function_exists('register_sidebar'))
            register_sidebar(array(
                    'id'=>'km-sideabar',
                    'name' => 'Shop Sidebar',
                    'before_title' => '<h3>',
                    'after_title' => '</h3>',
                )
            );
    }

    /**
     *  Includes Library Classes
     */

    public function include_classes()
    {
        require_once "classes/class-seller-product-management.php";
        require_once "lib/km-brands-widget.php";
        require_once "lib/km-post-widget.php";
        require_once "lib/km-search-widget.php";
        require_once "woocommerce-favourites/lib/km-favourites-product-class.php";
        require_once "woocommerce-favourites/lib/km-favourites-shop-class.php";
    }

    /**
     *  Include function file
     */

    public function include_function_files()
    {
        require_once "lib/km-global-hooks.php";
        require_once "lib/km-global-functions.php";
        require_once "lib/km-frontend-product-managememt-filters.php";
        require_once "lib/km-frontend-product-management-action-hooks.php";
        require_once "woocommerce-favourites/km-woocommerce-favourites-filter.php";
    }

    /**
     * initialize Instance of library classes
     */

    public function init_instance()
    {
        $this->product_management = new KM_SellerProduct;
    }

}

$kmi = new KM_init();

