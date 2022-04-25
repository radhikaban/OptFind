<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/20/2017
 * Time: 3:51 PM
 */
if (!defined('ABSPATH')) die('-1');

class KMBrands_Shortcode
{
    function __construct()
    {
        // We safely integrate with VC with this hook
        add_action('init', array($this, 'integrateWithVC'));

        // Use this when creating a shortcode addon
        add_shortcode('km-all-brands', array(
            $this, 'all_brands_shortcode'));

    }

    public function integrateWithVC()
    {
        // Check if Visual Composer is installed
        if (!defined('WPB_VC_VERSION')) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array($this, 'km_show_vc_warnings'));
            return;
        }

        /*
        Add your Visual Composer logic here.
        Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

        More info: http://kb.wpbakery.com/index.php?title=Vc_map
        */
        $available_image_sizes_adapted = array();
        $available_image_sizes = get_intermediate_image_sizes();

        foreach ($available_image_sizes as $image_size) {
            $available_image_sizes_adapted[$image_size] = $image_size;
        }

        vc_map(array(
            "name" => __("Brands Sort List", 'vc_extend'),
            "description" => __("Show Brands List Sorting", 'vc_extend'),
            "base" => "km-all-brands",
            "class" => "",
            "icon" => PWB_PLUGIN . '/assets/img/icon_pwb.jpg',
            "category" => "WooCommerce",
            "params" => array(
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "heading" => __("Brands per page", "perfect-woocommerce-brands"),
                    "param_name" => "per_page",
                    "value" => "10",
                    "description" => __("Show x brands per page", "perfect-woocommerce-brands")
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Brand logo size", "perfect-woocommerce-brands"),
                    "param_name" => "image_size",
                    "admin_label" => true,
                    "value" => $available_image_sizes_adapted
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Order by", "perfect-woocommerce-brands"),
                    "param_name" => "order_by",
                    "admin_label" => true,
                    "value" => array(
                        'name' => 'name',
                        'slug' => 'slug',
                        'term_id' => 'term_id',
                        'id' => 'id',
                        'description' => 'description',
                    )
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Order", "perfect-woocommerce-brands"),
                    "param_name" => "order",
                    "admin_label" => true,
                    "value" => array(
                        'ASC' => 'ASC',
                        'DSC' => 'DSC'
                    )
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Title position", "perfect-woocommerce-brands"),
                    "param_name" => "title_position",
                    "admin_label" => true,
                    "value" => array(
                        __("Before image", "perfect-woocommerce-brands") => 'before',
                        __("After image", "perfect-woocommerce-brands") => 'after'
                    )
                ),
                array(
                    "type" => "checkbox",
                    "holder" => "div",
                    "heading" => __("Hide empty", "perfect-woocommerce-brands"),
                    "param_name" => "hide_empty",
                    "description" => __("Hide brands that have not been assigned to any product", "perfect-woocommerce-brands")
                )
            )


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
    public function all_brands_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'per_page' => "10",
            'image_size' => "thumbnail",
            'hide_empty' => false,
            'order_by' => 'name',
            'order' => 'ASC',
            'title_position' => 'before'
        ), $atts, 'pwb-all-brands');

        $hide_empty = true;
        if ($atts['hide_empty'] != 'true') {
            $hide_empty = false;
        }

        ob_start();

        $result = array();
        add_filter('terms_clauses', array($this, 'wpse_178511_get_terms_fields'), 10, 3);
        $brands = get_terms('pwb-brand', array(
            'hide_empty' => $hide_empty,
            'order_by' => $atts['order_by'],
            'order' => $atts['order'],
            'surname' => isset($_GET['km-sort-order']) && !empty($_GET['km-sort-order']) ? $_GET['km-sort-order'] : '',
        ));
        remove_filter('terms_clauses', array($this, 'wpse_178511_get_terms_fields'), 10, 3);
        if (is_array($brands) && count($brands) > 0) {
            $result = $brands;
        }


        foreach (range('a', 'z') as $char) {
            ?>
            <a class="brands_tags" href="<?php get_the_permalink() ?>?km-sort-order=<?php echo $char ?>"><?php echo ucfirst($char) ?></a>
            <?php
        }
        ?>
        <div class="pwb-all-brands">
            <?php static::pagination($result, $atts['per_page'], $atts['image_size'], $atts['title_position']); ?>
        </div>


        <?php

        return ob_get_clean();
    }

    public static function pagination($display_array, $show_per_page, $image_size, $title_position)
    {
        $page = 1;

        if (isset($_GET['km-page']) && filter_var($_GET['km-page'], FILTER_VALIDATE_INT) == true) {
            $page = $_GET['km-page'];
        }


        $page = ($page == 0 ? 1 : $page);
        $start = ($page - 1) * $show_per_page;

        $outArray = array_slice($display_array, $start, $show_per_page);

        //pagination links
        $total_elements = count($display_array);
        $pages = ((int)$total_elements / (int)$show_per_page);
        $pages = ceil($pages);
        if ($pages >= 1 && $page <= $pages) {

            ?>
            <div class="km-brands-outer">
                <?php
                foreach ($outArray as $brand) {
                    $brand_id = $brand->term_id;
                    $brand_name = $brand->name;
                    $brand_link = get_term_link($brand_id);

                    $attachment_id = get_term_meta($brand_id, 'pwb_brand_image', 1);
                    $attachment_html = $brand_name;
                    if ($attachment_id != '') {
                        $attachment_html = wp_get_attachment_image($attachment_id, $image_size);
                    }

                    ?>
                    <div class="pwb-brands-col6">

                        <?php if ($title_position != 'after'): ?>
                            <p>
                                <?php echo $brand_name; ?>
                                <small>(<?php echo $brand->count; ?>)</small>
                            </p>
                        <?php endif; ?>

                        <div>
                            <a href="<?php echo $brand_link; ?>"
                               title="<?php _e('View brand', 'perfect-woocommerce-brands'); ?>"><?php echo $attachment_html; ?></a>
                        </div>

                        <?php if ($title_position == 'after'): ?>
                            <p>
                                <?php echo $brand_name; ?>
                                <small>(<?php echo $brand->count; ?>)</small>
                            </p>
                        <?php endif; ?>

                    </div>
                    <?php
                }
                ?>
            </div>
            <?php


            echo '<div class="pwb-pagination-wrapper">';
            echo self::displayPaginationBelow($show_per_page, $page, $total_elements);
            echo '</div>';

        } else {
            echo __('No Brands Found', 'perfect-woocommerce-brands');
        }

    }


    public static function displayPaginationBelow($per_page, $page, $total)
    {

        $adjacents = "2";

        $page = ($page == 0 ? 1 : $page);
        $start = ($page - 1) * $per_page;
        $page_url = '';
        $prev = $page - 1;
        $next = $page + 1;
        $setLastpage = ceil($total / $per_page);
        $lpm1 = $setLastpage - 1;
        $setPaginate = "";

       


        if ($setLastpage > 1) {
            $setPaginate .= "<ul class='setPaginate'>";
            //$setPaginate .= "<li class='setPage'>Page $page of $setLastpage</li>";
            if ($setLastpage < 7 + ($adjacents * 2)) {
                for ($counter = 1; $counter <= $setLastpage; $counter++) {
                    if ($counter == $page) {
                        $setPaginate .= "<li><a class='current_page'>$counter</a></li>";
                    } else {
                        $page_url = add_query_arg('km-page', $counter);
                        $setPaginate .= "<li><a href='{$page_url}'>$counter</a></li>";
                    }

                }
            } elseif ($setLastpage > 5 + ($adjacents * 2)) {
                if ($page < 1 + ($adjacents * 2)) {
                    for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                        if ($counter == $page) {
                            $setPaginate .= "<li><a class='current_page'>$counter</a></li>";
                        } else {
                            $page_url = add_query_arg('km-page', $counter);
                            $setPaginate .= "<li><a href='{$page_url}'>$counter</a></li>";
                        }
                    }
                    $setPaginate .= "<li class='dot'>...</li>";
                    $page_url = add_query_arg('km-page', $lpm1);
                    $setPaginate .= "<li><a href='{$page_url}'>$lpm1</a></li>";
                    $page_url = add_query_arg('km-page', $setLastpage);
                    $setPaginate .= "<li><a href='{$page_url}'>$setLastpage</a></li>";
                } elseif ($setLastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                    $page_url = add_query_arg('km-page', 1);
                    $setPaginate .= "<li><a href='{$page_url}'>1</a></li>";
                    $page_url = add_query_arg('km-page', 2);
                    $setPaginate .= "<li><a href='{$page_url}'>2</a></li>";
                    $setPaginate .= "<li class='dot'>...</li>";
                    for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                        if ($counter == $page) {
                            $setPaginate .= "<li><a class='current_page'>$counter</a></li>";
                        } else {
                            $page_url = add_query_arg('km-page', $counter);
                            $setPaginate .= "<li><a href='{$page_url}'>$counter</a></li>";
                        }
                    }
                    $setPaginate .= "<li class='dot'>..</li>";
                    $page_url = add_query_arg('km-page', $lpm1);
                    $setPaginate .= "<li><a href='{$page_url}'>$lpm1</a></li>";
                    $page_url = add_query_arg('km-page', $setLastpage);
                    $setPaginate .= "<li><a href='{$page_url}'>$setLastpage</a></li>";
                } else {
                    $page_url = add_query_arg('km-page', 1);
                    $setPaginate .= "<li><a href='{$page_url}'>1</a></li>";
                    $page_url = add_query_arg('km-page', 2);
                    $setPaginate .= "<li><a href='{$page_url}'>2</a></li>";
                    $setPaginate .= "<li class='dot'>..</li>";
                    for ($counter = $setLastpage - (2 + ($adjacents * 2)); $counter <= $setLastpage; $counter++) {
                        if ($counter == $page) {
                            $setPaginate .= "<li><a class='current_page'>$counter</a></li>";
                        } else {
                            $page_url = add_query_arg('km-page', $counter);
                            $setPaginate .= "<li><a href='{$page_url}'>$counter</a></li>";
                        }
                    }
                }
            }




            if ($page < $counter - 1) {
                $page_url = add_query_arg('km-page', $next);
                $setPaginate .= "<li><a href='{$page_url}'>Next</a></li>";
               
            } else {
                $setPaginate .= "<li><a class='current_page'>Next</a></li>";
               
            }

            $setPaginate .= "</ul>\n";
        }


        return $setPaginate;
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
new KMBrands_Shortcode();