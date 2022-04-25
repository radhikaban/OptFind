<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package storefront
 */
if(session_id() == '')
    session_start();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico"/>
    <link rel="icon" type="image/x-icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico"/>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

    <?php wp_head(); ?>
    <script type="text/javascript">
        jQuery(window).scroll(function () {
            var scroll = jQuery(window).scrollTop();
            if (scroll >= 200) {
                jQuery("header.site-header").addClass("header-sm");
            } else {
                jQuery("header.site-header").removeClass("header-sm");
            }
        });
    </script>


</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed site">
    <?php
    do_action('storefront_before_header'); ?>

    <header id="masthead" class="site-header" role="banner" style="<?php storefront_header_styles(); ?>">

        <div class="top-bar">
            <div class="col-full">
                <div class="menu_toggle"><i class="fa fa-bars"></i></div>
                <ul>
                    <li class="handheld-menu"><a href="<?php echo site_url().'/wishlist/' ?>">My
                                Wishlist </a>
                    </li>
                    <?php if (is_user_logged_in()): ?>
                        <li class="handheld-menu"><a href="javascript:void(0)">My
                                Account <i class="fa fa-angle-down"></i></a>

                            <?php
                            wp_nav_menu(array(
                                'theme_location' => 'handheld',
                                'fallback_cb' => '',
                                'container_class' => 'handheld-wrap'
                            ));
                            ?>
                        </li>
                        <?php
                        if (is_user_wcmp_vendor(get_current_user_id())) {
                            ?>
                            <li><a href="<?php echo wcmp_get_vendor_dashboard_endpoint_url('dashboard') ?>">Vendor
                                    Dashboard</a></li>
                            <?php
                        }
                        ?>

                        <li><a href="<?php echo wp_logout_url(home_url()) ?>">Logout</a></li>
                    <?php else : ?>
                        <li><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>">My
                                Account</a></li>
                        <li><a href="<?php  echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>">Login</a>/<a
                                    href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) ?>">Register</a>
                        </li>
                    <?php endif; ?>
            </div>
        </div>

        <div class="col-full">

            <?php
            /**
             * Functions hooked into storefront_header action
             *
             * @hooked storefront_skip_links                       - 0
             * @hooked storefront_social_icons                     - 10
             * @hooked storefront_site_branding                    - 20
             * @hooked storefront_secondary_navigation             - 30
             * @hooked storefront_product_search                   - 40
             * @hooked storefront_primary_navigation_wrapper       - 42
             * @hooked storefront_primary_navigation               - 50
             * @hooked storefront_header_cart                      - 60
             * @hooked storefront_primary_navigation_wrapper_close - 68
             */
            do_action('storefront_header');


            ?>

        </div>

        <div class="quick-menu">
            <div class="col-full">
                <?php do_action('storefront_header_custom'); ?>
            </div>
        </div>
    </header><!-- #masthead -->

    <?php
    /**
     * Functions hooked in to storefront_before_content
     *
     * @hooked storefront_header_widget_region - 10
     */
    do_action('storefront_before_content'); ?>

    <div id="content" class="site-content" tabindex="-1">
        <div class="col-full">

<?php
/**
 * Functions hooked in to storefront_content_top
 *
 * @hooked woocommerce_breadcrumb - 10
 */
do_action('storefront_content_top');
