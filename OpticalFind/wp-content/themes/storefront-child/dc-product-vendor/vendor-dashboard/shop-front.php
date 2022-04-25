<?php
/*
 * The template for displaying vendor dashboard
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor-dashboard/shop-front.php
 *
 * @author 	WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.4.5
 */
if (!defined('ABSPATH')) {
    // Exit if accessed directly
    exit;
}
global $WCMp;
$user = wp_get_current_user();
$vendor = get_wcmp_vendor($user->ID);
if ($vendor) :
    $vendor_hide_description = get_user_meta($user->ID, '_vendor_hide_description', true);
    $vendor_hide_email = get_user_meta($user->ID, '_vendor_hide_email', true);
    $vendor_hide_address = get_user_meta($user->ID, '_vendor_hide_address', true);
    $vendor_hide_phone = get_user_meta($user->ID, '_vendor_hide_phone', true);
    $vendor_hide_message_to_buyers = get_user_meta($user->ID, '_vendor_hide_message_to_buyers', true);

    $is_hide_option_show_enable = apply_filters('is_hide_option_show_enable', true);
    ?>
    <div class="wcmp_headding2"><?php _e('General', 'dc-woocommerce-multi-vendor'); ?></div>
    <form method="post" name="shop_settings_form" class="wcmp_shop_settings_form" enctype="multipart/form-data">
        <?php do_action('wcmp_before_shop_front'); ?>
        <div class="wcmp_form1">
            <p><?php _e('Store Name *', 'dc-woocommerce-multi-vendor'); ?></p>
            <input class="no_input" readonly type="text" name="vendor_page_title"
                   value="<?php echo isset($vendor_page_title['value']) ? $vendor_page_title['value'] : ''; ?>"
                   placeholder="<?php _e('Enter Your Store Name Here', 'dc-woocommerce-multi-vendor'); ?>">
            <p><?php _e('Store Tagline', 'dc-woocommerce-multi-vendor'); ?></p>
            <input class="no_input" readonly type="text" name="vendor_tagline"
                   value="<?php echo isset($vendor_tagline['value']) ? $vendor_tagline['value'] : ''; ?>"
                   placeholder="<?php _e('Enter Your Store Tagline Here', 'dc-woocommerce-multi-vendor'); ?>">
            <p><?php _e('Store Owner Name *', 'dc-woocommerce-multi-vendor'); ?></p>
            <input class="no_input" readonly type="text" name="vendor_shop_owner"
                   value="<?php echo isset($vendor_shop_owner['value']) ? $vendor_shop_owner['value'] : ''; ?>"
                   placeholder="<?php _e('Enter Owner Name Here', 'dc-woocommerce-multi-vendor'); ?>">
            <p><?php _e(' Store Slug *', 'dc-woocommerce-multi-vendor'); ?></p>
            <span style="display:block;" class="txt"><?php
                $dc_vendors_permalinks_array = get_option('dc_vendors_permalinks');
                if (isset($dc_vendors_permalinks_array['vendor_shop_base']) && !empty($dc_vendors_permalinks_array['vendor_shop_base'])) {
                    $store_slug = trailingslashit($dc_vendors_permalinks_array['vendor_shop_base']);
                } else {
                    $store_slug = trailingslashit('vendor');
                }
                echo $shop_page_url = trailingslashit(get_home_url());
                echo $store_slug;
                ?><input class="small no_input" readonly type="text" name="vendor_page_slug" readonly
                         value="<?php echo isset($vendor_page_slug['value']) ? $vendor_page_slug['value'] : ''; ?>"
                         placeholder="<?php _e('Enter Your Store Name Here', 'dc-woocommerce-multi-vendor'); ?>">
            </span>
            <p> <?php _e('Shop Description', 'dc-woocommerce-multi-vendor'); ?>
                <?php if (get_wcmp_vendor_settings('is_hide_option_show', 'capabilities', 'miscellaneous') && $is_hide_option_show_enable) { ?>
                <span class="input-group-addon beautiful"><input type="checkbox" name="vendor_hide_description"
                                                                 value="Enable" <?php if ($vendor_hide_description == 'Enable') echo 'checked=checked'; ?>><span>  <?php _e('Hide from user', 'dc-woocommerce-multi-vendor'); ?></span></span>
            </p>
            <?php } ?>
            <textarea class="no_input" readonly name="vendor_description" cols="" rows=""
            ><?php echo isset($vendor_description['value']) ? $vendor_description['value'] : ''; ?></textarea>


            <?php if (isset($vendor_message_to_buyers)) { ?>
                <p> <?php _e('Message to Buyers', 'dc-woocommerce-multi-vendor'); ?></p>
                <textarea class="no_input" readonly name="vendor_message_to_buyers" cols="" rows=""
                ><?php echo isset($vendor_message_to_buyers['value']) ? $vendor_message_to_buyers['value'] : ''; ?></textarea>
            <?php } ?>
            <div class="half_part">
                <p><?php _e('Phone', 'dc-woocommerce-multi-vendor'); ?>
                    <?php if (get_wcmp_vendor_settings('is_hide_option_show', 'capabilities', 'miscellaneous') && $is_hide_option_show_enable) { ?>
                        <span class="input-group-addon beautiful">
                            <input type="checkbox" name="vendor_hide_phone"
                                   value="Enable" <?php if ($vendor_hide_phone == 'Enable') echo 'checked=checked'; ?> >
                            <span> <?php _e('Hide from user', 'dc-woocommerce-multi-vendor'); ?></span> </span>
                    <?php } ?>
                </p>
                <input class="no_input" id="vendor_phone" type="text" name="vendor_phone" placeholder=""
                       value="<?php echo isset($vendor_phone['value']) ? $vendor_phone['value'] : ''; ?>">
            </div>
            <div class="half_part">
                <p><?php _e('Email *', 'dc-woocommerce-multi-vendor'); ?>
                    <?php if (get_wcmp_vendor_settings('is_hide_option_show', 'capabilities', 'miscellaneous') && $is_hide_option_show_enable) { ?>
                        <span class="input-group-addon beautiful">
                            <input type="checkbox" name="vendor_hide_email"
                                   value="Enable" <?php if ($vendor_hide_email == 'Enable') echo 'checked=checked'; ?>>
                            <span><?php _e('Hide from user', 'dc-woocommerce-multi-vendor'); ?></span> </span>
                    <?php } ?>
                </p>
                <input class="no_input vendor_email" readonly type="text" disabled placeholder=""
                       value="<?php echo isset($vendor->user_data->user_email) ? $vendor->user_data->user_email : ''; ?>">
            </div>
            <div class="clear"></div>
            <p><?php _e('Address', 'dc-woocommerce-multi-vendor'); ?>
                <?php if (get_wcmp_vendor_settings('is_hide_option_show', 'capabilities', 'miscellaneous') && $is_hide_option_show_enable) { ?>
                    <span class="input-group-addon beautiful">
                        <input type="checkbox" name="vendor_hide_address"
                               value="Enable" <?php if ($vendor_hide_address == 'Enable') echo 'checked=checked'; ?>>
                        <span><?php _e(' Hide from user', 'dc-woocommerce-multi-vendor'); ?></span> </span>
                <?php } ?>
            </p>

<!--            <input  type="text" class="no_input" name="vendor_auto_address" id="searchaddress" >-->


            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCc75Q9kXqU-DXijJUzbBwMYYtdXCfFAH8&libraries=places"></script>
            <script>

                //street_number
//route


                // administrative_area_level_1

//country
//postal_code
//locality
                function parseGoogleResponse(components) {
                    var street = '';
                    _.each(components, function(component) {
                        _.each(component.types, function(type) {
                            if (type === 'street_number') {
                                street = component.long_name;

                            }

                            if (type === 'route') {
                                street = street+ " "+ component.long_name;
                                $("input[name=vendor_address_1]").val(street)
                            }
                            if (type === 'locality' ) {
                                $("input[name=vendor_city]").val(component.long_name)
                            }
                            if (type === 'administrative_area_level_1' ) {
                                $("input[name=vendor_state]").val(component.long_name)
                            }

                            if (type === 'country') {
                                $("input[name=vendor_country]").val(component.long_name)
                            }
                            if (type === 'postal_code') {
                                $("input[name=vendor_postcode]").val(component.long_name)
                            }
                        })
                    })
                }

                jQuery(document).ready(function () {

                    var placeSearch, autocomplete;
                    var componentForm = {
                        street_number: 'short_name',
                        route: 'long_name',
                        locality: 'long_name',
                        administrative_area_level_1: 'long_name',
                        country: 'long_name',
                        postal_code: 'short_name'
                    };


                    var input = document.getElementById('searchaddress');

                    console.log(input);
                    var options = {
                        componentRestrictions: {
                            country: 'ca'
                        }
                    };

                    var autocomplete = new google.maps.places.Autocomplete(input, options);

                    google.maps.event.addListener(autocomplete, 'place_changed', function () {

                        var place = autocomplete.getPlace();

                        parseGoogleResponse(place.address_components);

                        $("input[name=vendor_lat]").val(place.geometry.location.lat())
                        $("input[name=vendor_lng]").val(place.geometry.location.lng())
                    });

                })



            </script>

            <input class="no_input readonly_address" readonly type="text" placeholder="Address line 1" name="vendor_address_1"
                   value="<?php echo isset($vendor_address_1['value']) ? $vendor_address_1['value'] : ''; ?>">
            <input class="no_input readonly_address" readonly type="hidden" placeholder="Address line 2" name="vendor_address_2"
                   value="<?php echo isset($vendor_address_2['value']) ? $vendor_address_2['value'] : ''; ?>">

            <div class="half_part">


                <input class="no_input readonly_address" readonly type="text" placeholder="City" name="vendor_city"
                       value="<?php echo isset($vendor_city['value']) ? $vendor_city['value'] : ''; ?>">

                <input class="no_input readonly_address" readonly type="text" placeholder="Country" name="vendor_country"
                       value="<?php echo isset($vendor_country['value']) ? $vendor_country['value'] : ''; ?>">


            </div>
            <div class="half_part">

                <?php
                $state = WC()->countries->get_states('CA');


                ?>

                <select name="vendor_state">
                    <?php foreach ($state as $k => $v): ?>
                        <option
                            <?php echo (isset($vendor_state['value']) && $vendor_state['value'] == $k) ? 'selected' : '' ?>
                                value="<?php echo $k ?>"><?php echo $v ?></option>
                    <?php endforeach; ?>

                </select>

<!--                <input class="no_input readonly_address" readonly type="text" placeholder="Province" name="vendor_state"-->
<!--                       value="--><?php //echo isset($vendor_state['value']) ? $vendor_state['value'] : ''; ?><!--">-->
                <input class="no_input readonly_address" readonly type="text" placeholder="Postal Code"
                       name="vendor_postcode"
                       value="<?php echo isset($vendor_postcode['value']) ? $vendor_postcode['value'] : ''; ?>">

            </div>
              <?php
            $is_vendor_add_external_url_field = apply_filters('is_vendor_add_external_url_field', true);
            if ($WCMp->vendor_caps->vendor_capabilities_settings('is_vendor_add_external_url') && $is_vendor_add_external_url_field) {
                ?>
                <div class="half_part">
                    <input class="no_input" readonly type="text" placeholder="External store URL"
                           name="vendor_external_store_url"
                           value="<?php echo isset($vendor_external_store_url['value']) ? $vendor_external_store_url['value'] : ''; ?>">
                </div>
                <div class="half_part">
                    <input class="no_input" readonly type="text" placeholder="External store URL Label"
                           name="vendor_external_store_label"
                           value="<?php echo isset($vendor_external_store_label['value']) ? $vendor_external_store_label['value'] : ''; ?>">
                </div>
                <?php
            }
            ?>
        </div>

        <div class="wcmp_headding2 moregap"><?php _e('Media Files', 'dc-woocommerce-multi-vendor'); ?></div>


        <div class="wcmp_media_block">



            <label for="store_logo_upload">
                Store Logo

                <img id="vendor_image_ele"
                     src="<?php echo (isset($vendor_image['value']) && (!empty($vendor_image['value']))) ? $vendor_image['value'] : $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg'; ?>">


                <input style="display: block; position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0,0,0,0);
    border: 0;" accept="image/*" data-ele="vendor_image_ele" class="km_stroe_image_upload"
                       type="file"
                       id="store_logo_upload"
                       name="vendor_image_i">

                <input type="hidden"
                       id="vendor_image_ele_image" name="vendor_image_i_image"
                       value="<?php echo (isset($vendor_image['value']) && (!empty($vendor_image['value']))) ? $vendor_image['value'] : '' ?>">


                <input data-id="vendor_image_ele"
                       data-src="<?php echo $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg' ?>"
                       style="<?php echo ((isset($vendor_image['value']) && !empty($vendor_image['value']) && $vendor_image['value'] != $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg')) ? '' : 'display:none' ?>"
                       type="button" class="remove_button vendor_remove_button wcmp_black_btn moregap two_third_part"

                       value="<?php _e('Remove', 'dc-woocommerce-multi-vendor') ?>"/>

            </label>
            <div class="clear"></div>
        </div>
<!--        <div class="wcmp_media_block">-->
<!--            <label>-->
<!--                Store Banner-->
<!--                <img id="vendor_banner_ele"-->
<!--                     src="--><?php //echo (isset($vendor_banner['value']) && (!empty($vendor_banner['value']))) ? $vendor_banner['value'] : $WCMp->plugin_url . 'assets/images/banner_placeholder.jpg'; ?><!--">-->
<!--                <input style="display: none" accept="image/*" data-ele="vendor_banner_ele"-->
<!--                       class="km_stroe_image_upload_banner" type="file"-->
<!--                       name="vendor_banner_i">-->
<!---->
<!--                <input type="hidden"-->
<!--                       id="vendor_banner_ele_image" name="vendor_banner_i_image"-->
<!--                       value="--><?php //echo (isset($vendor_banner['value']) && (!empty($vendor_banner['value']))) ? $vendor_banner['value'] : '' ?><!--">-->
<!---->
<!---->
<!---->
<!--                <input data-id="vendor_banner_ele"-->
<!--                       data-src="--><?php //echo $WCMp->plugin_url . 'assets/images/banner_placeholder.jpg'; ?><!--"-->
<!--                       style="--><?php //echo ((isset($vendor_banner['value']) && !empty($vendor_banner['value']) && $vendor_banner['value'] != $WCMp->plugin_url . 'assets/images/banner_placeholder.jpg'))? '' : 'display:none' ?><!--"-->
<!--                       type="button" class="remove_button vendor_remove_button wcmp_black_btn moregap two_third_part"-->
<!---->
<!--                       value="--><?php //_e('Remove', 'dc-woocommerce-multi-vendor') ?><!--"/>-->
<!---->
<!--            </label>-->
<!--            <div class="clear"></div>-->
<!--        </div>-->
<!--        <div class="wcmp_media_block">-->
<!--            <label>-->
<!--                Owner Picture-->
<!--                <img id="vendor_fshop_ele"-->
<!--                     src="--><?php //echo (isset($vendor_fshop['value']) && (!empty($vendor_fshop['value']))) ? $vendor_fshop['value'] : $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg'; ?><!--">-->
<!---->
<!---->
<!--                <input style="display: none" accept="image/*" data-ele="vendor_fshop_ele" class="km_stroe_image_upload"-->
<!--                       type="file"-->
<!--                        name="vendor_fshop_i">-->
<!---->
<!--                <input type="hidden"-->
<!--                       name="vendor_fshop_i_image"-->
<!--                       id="vendor_fshop_ele_image" value="--><?php //echo (isset($vendor_fshop['value']) && (!empty($vendor_fshop['value']))) ? $vendor_fshop['value'] : '' ?><!--">-->
<!---->
<!--                <input data-id="vendor_fshop_ele"-->
<!--                       data-src="--><?php //echo $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg' ?><!--"-->
<!--                       style="--><?php //echo ((isset($vendor_fshop['value']) && !empty($vendor_fshop['value']) && $vendor_fshop['value'] != $WCMp->plugin_url . 'assets/images/logo_placeholder.jpg') )? '' : 'display:none' ?><!--"-->
<!--                       type="button" class="remove_button vendor_remove_button wcmp_black_btn moregap two_third_part"-->
<!---->
<!--                       value="--><?php //_e('Remove', 'dc-woocommerce-multi-vendor') ?><!--"/>-->
<!---->
<!--            </label>-->
<!--            <div class="clear"></div>-->
<!--        </div>-->


        <script>
            jQuery(document).ready(function ($) {
                $('.vendor_remove_button').on('click', function () {
                    var id = jQuery(this).attr('data-id');
                    var src = jQuery(this).attr('data-src');
                    jQuery("#" + id).attr('src', src);
                    jQuery("#" + id + "_image").val('');
                    jQuery(this).hide();
                    jQuery(this).siblings('input[type=file]').val('');

                });
//
                $('.edit_shop_settings').on("click", function (e) {

                    $('#vendor_fshop_remove_button').show();


                });

                function readURL(input, ele, w, h) {



                    if (input.files && input.files[0]) {
                        var reader = new FileReader();

                        reader.onload = function (e) {

                            var image = new Image();

                            //Set the Base64 string return from FileReader as source.
                            image.src = e.target.result;

                            //Validate the File Height and Width.
                            image.onload = function () {
                                var height = this.height;
                                var width = this.width;


                                if (height > h || width > w) {
                                    jQuery.confirm("Height  and Width is too large . please upload image recommended size (" + w + "x" + h + ")");

                                    input.value = '';
                                    return false;
                                }


                                jQuery('#' + ele).attr('src', this.src);
                                jQuery(input).siblings('.vendor_remove_button').show();
                            };


                        }

                        reader.readAsDataURL(input.files[0]);

                    }
                }

                jQuery(".km_stroe_image_upload").change(function () {
                    readURL(this, jQuery(this).attr('data-ele'), 200, 200);
                });
                jQuery(".km_stroe_image_upload_banner").change(function () {
                    readURL(this, jQuery(this).attr('data-ele'), 1200, 345);

                });


            });
        </script>


        <div class="wcmp_headding2 moregap"><?php _e('Social Media', 'dc-woocommerce-multi-vendor'); ?></div>
        <div class="wcmp_media_block">
            <p><?php _e('Enter your Social Media profile URL below:', 'dc-woocommerce-multi-vendor'); ?></p>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/facebook.png' ?>" alt=""
                                        class="social_icon">
                <input class="long no_input" readonly type="text" name="vendor_fb_profile"
                       value="<?php echo isset($vendor_fb_profile['value']) ? $vendor_fb_profile['value'] : ''; ?>">
            </div>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/twitter.png' ?>" alt=""
                                        class="social_icon">
                <input class="long no_input" readonly type="text" name="vendor_twitter_profile"
                       value="<?php echo isset($vendor_twitter_profile['value']) ? $vendor_twitter_profile['value'] : ''; ?>">
            </div>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/linkedin_33x35.png' ?>"
                                        alt="" class="social_icon">
                <input class="long no_input" readonly type="text" name="vendor_linkdin_profile"
                       value="<?php echo isset($vendor_linkdin_profile['value']) ? $vendor_linkdin_profile['value'] : ''; ?>">
            </div>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/googleplus.png' ?>" alt=""
                                        class="social_icon">
                <input class="long no_input" readonly type="text" name="vendor_google_plus_profile"
                       value="<?php echo isset($vendor_google_plus_profile['value']) ? $vendor_google_plus_profile['value'] : ''; ?>">
            </div>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/youtube.png' ?>" alt=""
                                        class="social_icon ">
                <input class="long no_input" readonly type="text" name="vendor_youtube"
                       value="<?php echo isset($vendor_youtube['value']) ? $vendor_youtube['value'] : ''; ?>">
            </div>
            <div class="full_part"><img src="<?php echo $WCMp->plugin_url . 'assets/images/instagram.png' ?>" alt=""
                                        class="social_icon">
                <input class="long no_input" readonly type="text" name="vendor_instagram"
                       value="<?php echo isset($vendor_instagram['value']) ? $vendor_instagram['value'] : ''; ?>">
            </div>
            <div class="clear"></div>
        </div>
        <?php do_action('wcmp_after_shop_front'); ?>
        <?php do_action('other_exta_field_dcmv'); ?>
        <div class="action_div_space"></div>
        <p class="error_wcmp"><?php _e('* This field is required, you must fill some information.', 'dc-woocommerce-multi-vendor'); ?></p>
        <div class="action_div33">
            <button class="wcmp_oran4ge_btn" type="submit"
                    name="store_save"><?php _e('Save Options', 'dc-woocommerce-multi-vendor'); ?></button>
            <div class="clear"></div>
        </div>
    </form>
<?php endif; ?>
<script src="//cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.0/jquery-confirm.min.js"></script>
<script>
    jQuery(document).ready(function ($) {
        // validate the comment form when it is submitted
        jQuery(".wcmp_shop_settings_form").validate({
            rules: {
                vendor_fshop: {

                    accept: "image/*"
                },
                vendor_banner: {

                    accept: "image/*"
                },
                vendor_image: {

                    accept: "image/*"
                }
            }
        });
        $("#vendor_phone").mask("(999) 999-9999");
    });
</script>
