<?php
/**
* The template for displaying Tag cloud filters
*
* Override this template by copying it to yourtheme/woocommerce-filters/tag_cloud.php
*
* @author     BeRocket
* @package     WooCommerce-Filters/Templates
* @version  1.0.1
*/

foreach ( $terms as $term ) { ?>
    <li title="<?php echo $term->count; ?>" class="berocket_tag_cloud_element">
        <span>
            <input class="checkbox_<?php echo berocket_isset($term, 'term_id') ?>" autocomplete="off"
                type='checkbox' id='checkbox_<?php echo berocket_isset($term, 'term_id') ?>' data-term_id='<?php echo berocket_isset($term, 'term_id') ?>'
                data-term_slug='<?php echo urldecode(berocket_isset($term, 'slug')) ?>' data-filter_type='<?php echo berocket_isset($filter_type) ?>'
                data-term_name='<?php echo berocket_isset($term, 'name'); ?>' 
                data-taxonomy='<?php echo berocket_isset($term, 'taxonomy') ?>' data-operator='<?php echo berocket_isset($operator) ?>'
                <?php echo br_is_term_selected( $term, true ); ?>/>
                <label data-for='checkbox_<?php echo berocket_isset($term, 'term_id') ?>' for='checkbox_<?php echo berocket_isset($term, 'term_id') ?>'<?php if( br_is_term_selected( $term, true ) != '') echo ' class="berocket_checked"'; ?>><?php echo ( ! empty($icon_before_value) ? ( ( substr( $icon_before_value, 0, 3) == 'fa-' ) ? '<i class="fa '.$icon_before_value.'"></i>' : '<i class="fa"><img class="berocket_widget_icon" src="'.$icon_before_value.'" alt=""></i>' ) : '' ) . berocket_isset($term, 'name') . ( ! empty($icon_after_value) ? ( ( substr( $icon_after_value, 0, 3) == 'fa-' ) ? '<i class="fa '.$icon_after_value.'"></i>' : '<i class="fa"><img class="berocket_widget_icon" src="'.$icon_after_value.'" alt=""></i>' ) : '' ) ?></label>
        </span>
    </li>
<?php } ?>

<script>
    jQuery(document).ready(function () {
        var settings = {
            'height' : <?php echo br_get_value_from_array($tag_script_var, 'height'); ?>,
            'minFontSize' : <?php echo br_get_value_from_array($tag_script_var, 'min_font_size'); ?>,
            'maxFontSize' : <?php echo br_get_value_from_array($tag_script_varm, 'max_font_size'); ?>,
            'spacing' : 4,
            'maxCount' : <?php echo br_get_value_from_array($tag_script_var, 'tags_count'); ?>
        };
        jQuery('.berocket_aapf_widget-tag_cloud').doecloud(settings);
    });
</script>
<style>
    .berocket_aapf_widget.berocket_aapf_widget-tag_cloud{
        <?php if( ! empty($tag_script_var['height']) ) { ?>
        height: <?php echo $tag_script_var['height']; ?>px!important;
        <?php } ?>
    }
</style>
