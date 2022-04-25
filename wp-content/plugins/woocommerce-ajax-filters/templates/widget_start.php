<?php
$child_parent = berocket_isset($child_parent);
$is_child_parent = $child_parent == 'child';
?>
<div class="berocket_aapf_widget-wrapper">
    <?php if( $is_child_parent ) { ?>
    <div class="berocket_child_no_values">
        <?php echo berocket_isset($child_parent_no_values); ?>
    </div>
    <div class="berocket_child_previous">
        <?php echo berocket_isset($child_parent_previous); ?>
    </div>
    <div class="berocket_child_no_products">
        <?php echo berocket_isset($child_parent_no_products); ?>
    </div>
    <?php } ?>
    <div class="berocket_aapf_widget-title_div<?php if ( ! empty($is_hide_mobile) ) echo ' berocket_aapf_hide_mobile' ?>">
        <?php if ( empty($hide_collapse_arrow) ) { ?>
            <span class="berocket_aapf_widget_show <?php echo ( ! empty($widget_is_hide) ? 'show_button' : 'hide_button' ) ?> <?php echo ( ! empty($title) ? 'mobile_hide' : '' ) ?>"><i class="fa fa-angle-left "></i></span>
        <?php } ?>
        <?php if ( ! empty($description) ) { ?><span class="berocket_aapf_description"><i class="fa fa-info-circle"></i><div style="background-color:#<?php echo br_get_value_from_array($notuo, array('description', 'color')).'; border:1px solid #'.br_get_value_from_array($notuo, array('description_border', 'color')).';'?>"><h3 style="<?php echo br_get_value_from_array($uo, array('style', 'description_title'))?>"><?php echo $title; ?></h3><p style="<?php echo br_get_value_from_array($uo, array('style', 'description_text'))?>"><?php echo $description ?></p><div class="berocket_aapf_description_arrow"  style="background-color:#<?php echo br_get_value_from_array($notuo, array('description', 'color')).'; border:1px solid #'.br_get_value_from_array($notuo, array('description_border', 'color')).';'?>"></div></div></span><?php } ?>
        <?php if( ! empty($title) || ! empty($icon_before_title) || ! empty($icon_after_title) ) { ?><h3 class="widget-title berocket_aapf_widget-title" style="<?php echo ( empty($uo['style']['title']) ? '' : $uo['style']['title'] ) ?>"><span><?php echo ( ( ! empty($icon_before_title) ) ? ( ( substr( $icon_before_title, 0, 3) == 'fa-' ) ? '<i class="fa '.$icon_before_title.'"></i>' : '<i class="fa"><img class="berocket_widget_icon" src="'.$icon_before_title.'" alt=""></i>' ) : '' ).( empty($title) ? '' : $title ).( ( ! empty($icon_after_title) ) ? ( ( substr( $icon_after_title, 0, 3) == 'fa-' ) ? '<i class="fa '.$icon_after_title.'"></i>' : '<i class="fa"><img class="berocket_widget_icon" src="'.$icon_after_title.'" alt=""></i>' ) : '' ) ?></span></h3><?php } ?>
    </div>
    <ul class='berocket_aapf_widget <?php echo ( ! empty($widget_is_hide) ? 'berocket_style_none' : 'berocket_style_block' ) ?> <?php echo berocket_isset($product_count_style); ?> <?php if( $child_parent == 'child' ) echo $attribute.'_'.$child_parent.'_'.berocket_isset($child_parent_depth); ?> <?php if( ! empty($values_per_row) ) echo 'berocket_values_'.$values_per_row; ?><?php if ( ! empty($is_hide_mobile) ) echo ' berocket_aapf_hide_mobile' ?> <?php echo berocket_isset($class) ?> <?php echo berocket_isset($css_class) ?> <?php echo ( ( berocket_isset($type) == 'tag_cloud' ) ? 'berocket_aapf_widget-tag_cloud' : '' ) ?>'
        <?php echo berocket_isset($style) ?> data-scroll_theme='<?php echo berocket_isset($scroll_theme) ?>' data-child_parent="<?php echo $child_parent; ?>" <?php if( $child_parent == 'child' ) echo 'data-child_parent_depth="'.berocket_isset($child_parent_depth).'"'; ?>
        data-attribute='<?php echo $attribute; ?>' data-type='<?php echo $type; ?>' data-count_show='<?php if( ! empty($show_product_count_per_attr) ) echo 'show' ?>' data-cat_limit='<?php echo berocket_isset($cat_value_limit);  ?>'>
