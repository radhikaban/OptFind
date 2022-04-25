<?php
/**
 * Created by PhpStorm.
 * User: Sarab Sodhi
 * Date: 9/20/2017
 * Time: 3:51 PM
 */
if (!defined('ABSPATH')) die('-1');

class KMPost_Blog_Shortcode
{
    function __construct()
    {
        // We safely integrate with VC with this hook
        add_action('init', array($this, 'integrateWithVC'));

        // Use this when creating a shortcode addon
        add_shortcode('km-blog-post', array(
            $this, 'all_post_shortcode'));


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
            "name" => __("Blog Posts", 'vc_extend'),
            "description" => __("Show Brands List Sorting", 'vc_extend'),
            "base" => "km-blog-post",
            "class" => "",
            "category" => "WooCommerce",
            "params" => array(
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "heading" => __("Post per page", "KINEX"),
                    "param_name" => "per_page",
                    "value" => 1,
                    "description" => __("Show description on layout", "KINEX")
                ),

                array(
                    "type" => "dropdown",
                    "holder" => "div",
                    "heading" => __("Show Description", "KINEX"),
                    "param_name" => "show_description",
                    "value" => array(
                        'Yes' => 'yes',
                        'Yes' => 'no',
                    )
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Post Image size", "KINEX"),
                    "param_name" => "image_size",
                    "admin_label" => true,
                    "value" => $available_image_sizes_adapted
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Order by", "KINEX"),
                    "param_name" => "order_by",
                    "admin_label" => true,
                    "value" => array(
                        'title' => 'title',
                        'slug' => 'slug',
                        'ID' => 'ID',
                        'date' => 'date',
                        'modified' => 'modified',
                        'rand' => 'rand',
                        'menu_order' => 'menu_order',
                    )
                ),
                array(
                    "type" => "dropdown",
                    "heading" => __("Order", "KINEX"),
                    "param_name" => "order",
                    "admin_label" => true,
                    "value" => array(
                        'ASC' => 'ASC',
                        'DSC' => 'DSC'
                    )
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
    public function all_post_shortcode($atts, $content = null)
    {
        $atts = shortcode_atts(array(
            'per_page' => "4",
            'image_size' => "thumbnail",
            'order_by' => 'name',
            'order' => 'ASC',
            'show_description' => 'yes',
        ), $atts, 'pwb-all-brands');


        ob_start();
        $args = array(
            'posts_per_page' => $atts['per_page'],
            'orderby' => $atts['order_by'],
            'order' => $atts['order'],
            'post_type' => 'post',
            'post_status' => 'publish',
            'suppress_filters' => true
        );
        $the_query = new WP_Query($args); ?>

        <?php $i = 0 ?>
        <?php if ($the_query->have_posts()) : ?>
        <?php while ($the_query->have_posts()) : $the_query->the_post(); ?>
            <?php if ($i == 0) : ?>
                <div class="km-col-half">
                    <div class="post-<?php echo get_the_ID() ?>">
                        <?php

                        if (has_post_thumbnail())
                            the_post_thumbnail($atts['image_size']);
                        ?>
                        <h2 class="post_title"><?php echo get_the_title() ?></h2>
                        
                        <span class="date"><?php echo date('F d, Y', get_post_time()) ?> </span>
                        <a target="_blank" href="<?php echo get_permalink() ?> ">Read Full Story</a>
                    </div>
                </div>
                <?php $i++ ?>
            <?php else: ?>
                <?php if ($i == 1): ?>
                    <?php $i++; ?>
                    <div class="km-col-half">
                <?php endif; ?>

                <div class="post-<?php echo get_the_ID() ?>">
                    <h2 class="post_title"><?php echo get_the_title() ?></h2>

                    <?php if ($atts['show_description'] == 'yes') : ?>
                        <p class="post_description"> <?php echo get_the_excerpt() ?></p>
                    <?php endif; ?>
                    <span class="date"><?php echo date('F d, Y', get_post_time()) ?> </span>
                    <a target="_blank" href="<?php echo get_permalink() ?> ">Read Full Story</a>
                </div>

            <?php endif; ?>

        <?php endwhile; ?>
        <?php if ($i > 1): ?>
            </div>
        <?php endif; ?>
        <?php wp_reset_postdata(); ?>
    <?php endif; ?>


        <?php

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
new KMPost_Blog_Shortcode();