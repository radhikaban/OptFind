<?php
$random_name = rand();
$hiden_value = false;
$child_parent = berocket_isset($child_parent);
$is_child_parent = $child_parent == 'child';
$is_child_parent_or = ( $child_parent == 'child' || $child_parent == 'parent' );
$child_parent_depth = berocket_isset($child_parent_depth, false, 0);
if ( $child_parent == 'parent' ) {
    $child_parent_depth = 0;
}
$is_first = true;
if ( is_array(berocket_isset($terms)) ) {
    foreach ( $terms as $term ) {
        $meta_class = ( ! empty($show_product_count_per_attr) ? $term->count : '&nbsp;' );
        $meta_after = '';
        if ( !$is_child_parent || !$is_first ) {
            $meta_color = get_metadata( 'berocket_term', $term->term_id, $type );
        } else {
            $meta_color = 'R';
            ?>
            <li class="berocket_child_parent_sample"><ul>
            <?php
        }
        if( $type == 'color' ) {
            $meta_color = 'background-color: #'.br_get_value_from_array($meta_color, 0).';';
        } elseif( $type == 'image' ) {
            if ( ! empty($meta_color[0]) ) {
                if ( substr( $meta_color[0], 0, 3) == 'fa-' ) {
                    $meta_class = '<i class="fa '.$meta_color[0].'"></i>&nbsp;';
                    $meta_color = '';
                } else {
                    $meta_color = 'background: url('.$meta_color[0].') no-repeat scroll 50% 50% rgba(0, 0, 0, 0);';
                    $meta_class = '&nbsp;';
                }
                $meta_after = ( ! empty($show_product_count_per_attr) ? '<span class="berocket_aapf_count">'.$term->count.'</span>' : '' );
            } else {
                $meta_color = '';
                $meta_class = '';
            }
        }
        ?>
        <li class="berocket_term_parent_<?php echo berocket_isset($term, 'parent'); ?> <?php if ( $is_child_parent ) echo 'R__class__R '; ?><?php if( ! empty($hide_o_value) && berocket_isset($term, 'count') == 0 && ( !$is_child_parent || !$is_first ) ) { echo 'berocket_hide_o_value '; $hiden_value = true; }  if( ! empty($hide_sel_value) && br_is_term_selected( $term, true, $is_child_parent_or, $child_parent_depth ) != '' ) { echo 'berocket_hide_sel_value'; $hiden_value = true; } ?> berocket_checkbox_color<?php echo ( ! empty($use_value_with_color) ? ' berocket_color_with_value' : ' berocket_color_without_value' ) ?>">
            <span>
                <input id='checkbox_<?php echo str_replace ( '*' , '-' , berocket_isset($term, 'term_id')), str_replace ( '*' , '-' , berocket_isset($term, 'taxonomy')) ?>_<?php echo berocket_isset($random_name) ?>'
                       class="<?php echo ( empty($uo['class']['checkbox_radio']) ? '' : $uo['class']['checkbox_radio'] ) ?> checkbox_<?php echo str_replace ( '*' , '-' , berocket_isset($term, 'term_id')), str_replace ( '*' , '-' , berocket_isset($term, 'taxonomy')) ?>" 
                       type='checkbox' 
                       autocomplete="off" 
                       style="<?php echo ( empty($uo['style']['checkbox_radio']) ? '' : $uo['style']['checkbox_radio'] ) ?>" data-term_slug='<?php echo urldecode(berocket_isset($term, 'slug')) ?>' 
                       data-term_name='<?php echo ( ! empty($icon_before_value) ? ( ( substr( $icon_before_value, 0, 3) == 'fa-' ) ? '<i class="fa '.$icon_before_value.'"></i>' : '<i class="fa"><img class="berocket_widget_icon" src="'.$icon_before_value.'" alt=""></i>' ) : '' ) . berocket_isset($term, 'name') . ( ! empty($icon_after_value) ? ( ( substr( $icon_after_value, 0, 3) == 'fa-' ) ? '<i class="fa '.$icon_after_value.'"></i>' : '<i class="fa"><img class="berocket_widget_icon" src="'.$icon_after_value.'" alt=""></i>' ) : '' )?>' 
                       data-filter_type='<?php echo berocket_isset($filter_type) ?>' <?php if( ! empty($term->term_id) ) { ?>data-term_id='<?php echo $term->term_id ?>'<?php } ?> data-operator='<?php echo $operator ?>' 
                       data-term_ranges='<?php echo str_replace ( '*' , '-' , berocket_isset($term, 'term_id')) ?>' 
                       data-taxonomy='<?php echo berocket_isset($term, 'taxonomy') ?>' 
                       data-term_count='<?php echo berocket_isset($term, 'count') ?>' 
                       <?php echo br_is_term_selected( $term, true, $is_child_parent_or, $child_parent_depth ); ?> />
                <label data-for='checkbox_<?php echo str_replace ( '*' , '-' , berocket_isset($term, 'term_id')), str_replace ( '*' , '-' , berocket_isset($term, 'taxonomy')) ?>' 
                    class="berocket_label_widgets<?php if( br_is_term_selected( $term, true, $is_child_parent_or, $child_parent_depth ) != '') echo ' berocket_checked'; ?>">
                    <span class="berocket_color_span_block <?php if( empty($meta_after) ) echo 'berocket_aapf_count'; ?>" style="<?php echo $meta_color; ?>"><?php if( $type == 'color' ) echo '<span class="berocket_color_span_absolute"><span>'; ?><?php echo $meta_class; ?><?php if( $type == 'color' ) echo '</span></span>'; ?></span>
                    <?php echo ( ! empty($use_value_with_color) ? '<span class="berocket_color_text">' . ( ! empty($icon_before_value) ? ( ( substr( $icon_before_value, 0, 3) == 'fa-' ) ? '<i class="fa '.$icon_before_value.'"></i>' : '<i class="fa"><img class="berocket_widget_icon" src="'.$icon_before_value.'" alt=""></i>' ) : '' ) . $term->name . ( ! empty($icon_after_value) ? ( ( substr( $icon_after_value, 0, 3) == 'fa-' ) ? '<i class="fa '.$icon_after_value.'"></i>' : '<i class="fa"><img class="berocket_widget_icon" src="'.$icon_after_value.'" alt=""></i>' ) : '' ) . '</span>' : '' ); ?><?php echo berocket_isset($meta_after); ?>
                </label>
            </span>
        </li>
        <?php
        if ( $is_child_parent && $is_first ) {
            ?>
            </ul></li>
            <?php
            $is_first = false;
        }
    } ?>
    <?php if( $is_child_parent && is_array(berocket_isset($terms)) && count($terms) == 1 ) {
        if( BeRocket_AAPF_Widget::is_parent_selected($attribute, $child_parent_depth - 1) ) {
            echo '<li>'.$child_parent_no_values.'</li>';
        } else {
            echo '<li>'.$child_parent_previous.'</li>';
        }
    } else {
    if( $child_parent_no_values ) {?>
        <script>
        if ( typeof(child_parent_depth) == 'undefined' || child_parent_depth < <?php echo $child_parent_depth; ?> ) {
            child_parent_depth = <?php echo $child_parent_depth; ?>;
        }
        jQuery(document).ready(function() {
            if( child_parent_depth == <?php echo $child_parent_depth; ?> ) {
                jQuery('.woocommerce-info').text('<?php echo $child_parent_no_values; ?>');
            }
        });
        </script>
    <?php }
    }
    if( empty($hide_button_value) ) { ?>
        <li class="berocket_widget_show_values"<?php if( !$hiden_value ) echo 'style="display: none;"' ?>><?php _e('Show value(s)', 'BeRocket_AJAX_domain') ?><span class="show_button"></span></li>
    <div style="clear: both;"></div>
<?php } } ?>
