<?php

global $product;

?>

<div id="option-wrap" class="option-swatch">

    <div id="cross">

        <i class="fa fa-times"></i>

    </div>

    <div class="row">

        <div class="col-md-6">


            <?php global $wpdb; ?>

            <?php $sql = "SELECT DISTINCT  tr.term_taxonomy_id

                        FROM  {$wpdb->prefix}term_relationships as tr 

                        INNER JOIN {$wpdb->prefix}term_relationships as jt

                        ON tr.object_id = jt.object_id

                        WHERE tr.term_taxonomy_id

                        IN (

                        SELECT m.term_id

                        FROM {$wpdb->prefix}term_taxonomy as m 

                        WHERE m.taxonomy='lenses-category'
                        ) AND jt.term_taxonomy_id 

                        IN (

                        SELECT tt.term_id

                        FROM {$wpdb->prefix}term_taxonomy as tt 

                        WHERE tt.taxonomy='lenses-types'

                    )";
            $results = $wpdb->get_col($sql);

            ?>

            <?php $taxonomies = get_categories(array('include' => implode(",", $results), 'hide_empty' => false, 'order' => 'DESC', 'taxonomy' => 'lenses-category', 'style' => false, 'echo' => false)); ?>


            <input type="hidden" value="<?php echo $product->get_id() ?>" name="p_id">


            <?php foreach ($taxonomies as $taxonomy): ?>

                <img id="side-view<?php echo $taxonomy->term_id; ?>" class="side-view"
                     alt="<?php echo $taxonomy->name; ?>"
                     src="<?php echo site_url(''); ?>/wp-content/plugins/wc-custom-lenses/img/<?php echo $taxonomy->term_id ?>.png"

                     data-index="<?php echo $taxonomy->term_id; ?>">
            <?php endforeach; ?>


        </div>
        <div class="col-md-6">
            <h2 class="h1 title">Editor’s Collection <span>Prescription Entry</span></h2>
            <div id="select-option-tab" class="resp-tabs-list">

                <div id="km-loader"><i class="fa fa-spin fa-spinner"></i></div>

                <h2>Lenses Categories</h2>
                <section>
                    <div id="section-content-lens-category">


                        <?php kmcl_get_template('single-product/new-template/taxonomies.php', array('taxonomies' => $taxonomies)) ?>

                    </div>

                    <div class="text-center button-container">

                        <a class="btn btn-default btn-next" href="#">Next</a>

                    </div>
                </section>

                <h2>Add Prescription</h2>
                <section>

                    <div class="table">

                        <input type="hidden" value="false" id="skip_step" name="skip_step"/>
                        <div class="table-body table-row">

                            <div class="table-col-10">

                                <div class="table-col">


                                    <div class="td-element">OD (Right)</div>

                                    <div class="td-element">

                                        <div class="title">SPH</div>

                                        <div class="select-wrap">

                                            <select name="sph-right" id="sph-right" class="fancy-select">

                                                <option value="">0.00</option>

                                                <?php for ($i = 6.00; $i >= -12.00; $i = $i - 0.25): ?>

                                                    <option value="<?php echo number_format((float)$i, 2, '.', ''); ?>"><?php echo number_format((float)$i, 2, '.', ''); ?></option>

                                                <?php endfor; ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="td-element">

                                        <div class="title">CYL</div>

                                        <div class="select-wrap">

                                            <select name="cyl-right" id="cyl-right" class="fancy-select">

                                                <option value="">0.00</option>

                                                <?php for ($i = 3.75; $i >= -3.75; $i = $i - 0.25): ?>

                                                    <option value="<?php echo number_format((float)$i, 2, '.', ''); ?>"><?php echo number_format((float)$i, 2, '.', ''); ?></option>

                                                <?php endfor; ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="td-element">

                                        <div class="title">AXIS</div>

                                        <div class="select-wrap">

                                            <select name="axis-right" id="axis-right" class="fancy-select">

                                                <option value="">0</option>

                                                <?php for ($i = 1; $i <= 180; $i++): ?>

                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>

                                                <?php endfor; ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="td-element">

                                        <div class="add-no">OD</div>

                                        <div class="title">ADD <i class="fa  fa-question-circle"></i></div>

                                        <div class="select-wrap">

                                            <select name="add-right" id="add-right" class="fancy-select">

                                                <option value="0.00">0.00</option>

                                                <?php for ($i = 3.00; $i >= 1.00; $i = $i - 0.25): ?>

                                                    <option value="<?php echo number_format((float)$i, 2, '.', ''); ?>"><?php echo number_format((float)$i, 2, '.', ''); ?></option>

                                                <?php endfor; ?>

                                            </select>

                                        </div>

                                    </div>

                                </div>

                                <div class="table-col">

                                    <div class="td-element">OS<br>(Left)</div>

                                    <div class="td-element">

                                        <div class="select-wrap">

                                            <select name="sph-left" id="sph-left" class="fancy-select">

                                                <option value="">0.00</option>

                                                <?php for ($i = 6.00; $i >= -12.00; $i = $i - 0.25): ?>

                                                    <option value="<?php echo number_format((float)$i, 2, '.', ''); ?>"><?php echo number_format((float)$i, 2, '.', ''); ?></option>

                                                <?php endfor; ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="td-element">

                                        <div class="select-wrap">

                                            <select name="cyl-left" id="cyl-left" class="fancy-select">

                                                <option value="">0.00</option>

                                                <?php for ($i = 3.75; $i >= -3.75; $i = $i - 0.25): ?>

                                                    <option value="<?php echo number_format((float)$i, 2, '.', ''); ?>"><?php echo number_format((float)$i, 2, '.', ''); ?></option>

                                                <?php endfor; ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="td-element">

                                        <div class="select-wrap">

                                            <select name="axis-left" id="axis-left" class="fancy-select">

                                                <option value="">0</option>

                                                <?php for ($i = 1; $i <= 180; $i++): ?>

                                                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>

                                                <?php endfor; ?>

                                            </select>

                                        </div>

                                    </div>

                                    <div class="td-element">

                                        <div class="add-no">OS</div>

                                        <div class="select-wrap">

                                            <select name="add-left" id="add-left" class="fancy-select">

                                                <option value="">0.00</option>

                                                <?php for ($i = 3.00; $i >= 1.00; $i = $i - 0.25): ?>

                                                    <option value="<?php echo number_format((float)$i, 2, '.', ''); ?>"><?php echo number_format((float)$i, 2, '.', ''); ?></option>

                                                <?php endfor; ?>

                                            </select>

                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div class="table-col-2">

                                <div class="td-element">

                                    <div class="title">PD <i class="fa fa-question-circle"></i></div>

                                    <div class="select-wrap">

                                        <select name="pd" class="fancy-select twopd">

                                            <option value="">0.00</option>

                                            <?php for ($i = 47; $i <= 75; $i = $i + .5) : ?>

                                                <option value="<?php echo number_format((float)$i, 1, '.', ''); ?>"><?php echo number_format((float)$i, 1, '.', ''); ?></option>

                                            <?php endfor; ?>

                                        </select>

                                    </div>

                                    <div class="select-wrap" style="display: none">

                                        <select name="right-pd" class="fancy-select onepd">

                                            <option value="">0.00</option>

                                            <?php for ($i = 23; $i < 38; $i = $i + .5) : ?>

                                                <option value="<?php echo number_format((float)$i, 1, '.', ''); ?>"><?php echo number_format((float)$i, 1, '.', ''); ?></option>

                                            <?php endfor; ?>

                                        </select>

                                    </div>

                                    <div class="select-wrap" style="display: none">

                                        <select name="left-pd" class="fancy-select onepd">

                                            <option value="">0.00</option>

                                            <?php for ($i = 23; $i < 38; $i = $i + .5) : ?>

                                                <option value="<?php echo number_format((float)$i, 1, '.', ''); ?>"><?php echo number_format((float)$i, 1, '.', ''); ?></option>

                                            <?php endfor; ?>

                                        </select>

                                    </div>

                                    <div>

                                        <a href="#" class="pdno-check twopdonumber">I have two PD numbers</a>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="text-center button-container">

                            <a class="btn-goto" data-step="3" href="#">Continue Without Prescription </a>

                            <a data-step="2" class="btn btn-default notskip" href="#">Next</a>

                        </div>

                    </div>

                </section>

                <h2>Upload Prescription</h2>
                <section>


                    <div id="section-content-upload-prescription">
                        <label>

                            <input accept="image/*,application/pdf,application/vnd.ms-excel"
                                   class="km_upload_prescription"
                                   type="file"
                                   name="upload_image"/>

                            <!--                            <span class="prescription_filename"></span>-->
                            <!--                            <input style="display:none"-->
                            <!--                                   type="button"-->
                            <!--                                   class="remove_button prescription_remove_button wcmp_black_btn moregap two_third_part"-->
                            <!---->
                            <!--                                   value="-->
                            <?php //_e('Remove', 'dc-woocommerce-multi-vendor') ?><!--"/>-->

                        </label>

                        <script>
                            //                            jQuery(document).ready(function ($) {
                            //                                $('.prescription_remove_button').on('click', function () {
                            //                                    jQuery(this).hide();
                            //                                    jQuery(this).siblings('input[type=file]').val('');
                            //
                            //                                });
                            //
                            //                                document.getElementsByClassName('km_upload_prescription').change = function () {
                            //                                   alert('fd');
                            //                                    jQuery(this).siblings('.prescription_remove_button').show();
                            //                                    jQuery(this).siblings('.prescription_filename').html(this.value);
                            //
                            //                                    alert('Selected file: ' + this.value);
                            //                                };
                            //
                            //                            });
                        </script>
                    </div>

                    <div class="text-center button-container">

                        <a class="btn btn-default btn-next" href="#">Next</a>

                    </div>
                </section>

                <h2>Lens Treatments</h2>
                <section>

                    <div id="section-content-lens-type">

                    </div>

                    <div class="text-center button-container">


                        <p>

                            <a class="btn btn-default btn-next" href="#">Next</a>

                        </p>

                    </div>


                </section>


                <h2>Submit</h2>

                <section>

                    <div id="section-content-custom-lens">

                    </div>

                    <div class="text-center button-container">


                        <p>
                            <?php
                            $val = km_add_to_cart_button();
                            ?>
                            <?php if ($val) {

                                echo '<a  href="#" class="add_to_cart_more_offer btn btn-default">Add To Cart</a>';
                            } else {
                                echo ' <a class="btn btn-default btn-finish" href="#">Add To Cart</a>';
                            }


                            ?>

                        </p>

                    </div>


                </section>

            </div>


        </div>

    </div>

    <script>
        jQuery(function ($) {
            $("#dialog").dialog({
                autoOpen: false,

                show: {
                    effect: "blind",
                    duration: 1000
                },
                hide: {
                    effect: "explode",
                    duration: 1000
                },
                resizable:false,
                draggable:false,
                modal:true

            });

        });

    </script>


    <?php


    $val = km_add_to_cart_button();
    if ($val) :

        ?>

        <div id="dialog" title="Where You Want To Pickup Your Order">
            <?php km_more_vendor_by_distance(); ?>
        </div>
    <?php endif; ?>

</div>

<script>

    var returndata = true;

    var km_ajax_request = '';

    jQuery(document).ready(function () {


        jQuery(document).on('click', ".twopdonumber", function (e) {

            e.preventDefault();

            jQuery(".onepd").parent().show();

            jQuery(".twopd").parent().hide();


            jQuery(this).after('<div><a href="#" class="onepdonumber pdno-check">I have one PD number</a></div>');

            jQuery(this).remove();

        })

        jQuery(document).on('click', ".onepdonumber", function (e) {

            e.preventDefault();

            jQuery(".onepd").parent().hide();

            jQuery(".twopd").parent().show();


            jQuery(this).after('<div><a href="#" class="twopdonumber pdno-check">I have two PD number</a></div>');

            jQuery(this).remove();

        })


        /* Select Option End */

    });

    jQuery(document).ready(function () {

        //Horizontal Tab


        var form = jQuery('.cart');


        jQuery(document).on('click', ".add_to_cart_more_offer", function (e) {
            e.preventDefault();
            form.validate().settings.ignore = ":disabled,:hidden";
            if(form.valid()){
                jQuery("#dialog").dialog("open");
            }

        })





        var popupwizard = jQuery("#select-option-tab").steps({

            headerTag: "h2",

            bodyTag: "section",

            transitionEffect: "none",

            enablePagination: false,

            onStepChanged: function (event, currentIndex, priorIndex) {


                if (currentIndex == 2 && priorIndex == 1) {
                    if (jQuery("#skip_step").val() == 'true') {
                        popupwizard.steps('next')
                    }
                }

                if (currentIndex == 2 && priorIndex == 3) {
                    if (jQuery("#skip_step").val() == 'true') {
                        popupwizard.steps('previous')
                    }
                }

            },

            onStepChanging: function (event, currentIndex, newIndex) {



                // Allways allow previous action even if the current form is not valid!

                if (currentIndex > newIndex) {

                    return true;

                }


                // Needed in some cases if the user went back (clean up)

                if (currentIndex < newIndex) {

                    // To remove error styles

                    form.find(".body:eq(" + newIndex + ") label.error").remove();

                    form.find(".body:eq(" + newIndex + ") .error").removeClass("error");

                }



                form.validate().settings.ignore = ":disabled,:hidden";

                if (currentIndex == 2 && newIndex == 3) {

                    var left_cyl = parseFloat(jQuery('#cyl-left').val());

                    var right_cyl = parseFloat(jQuery('#cyl-right').val());


                    var left_sph = parseFloat(jQuery('#sph-left').val());

                    var right_sph = parseFloat(jQuery('#sph-right').val());


                    if (isNaN(left_sph)) {
                        left_sph = 0;
                    }

                    if (isNaN(right_sph)) {
                        right_sph = 0;
                    }

                    if (isNaN(left_cyl)) {
                        left_cyl = 0;
                    }

                    if (isNaN(right_cyl)) {
                        right_cyl = 0;
                    }

                    var left_dioptre = (0.5 * parseFloat(left_cyl)) + (parseFloat(left_sph));

                    var right_dioptre = (0.5 * parseFloat(right_cyl)) + (parseFloat(right_sph));


                    var correct_precription = true;

                    if ((left_dioptre < 0 || left_dioptre > 12) && (right_dioptre < 0 || right_dioptre > 12)) {

                        if (!confirm('Are you sure you are adding the correct prescription?')) {
                            correct_precription = false;
                        }
                    }

                    if (correct_precription) {

                        if (km_ajax_request) {
                            km_ajax_request.abort();
                        }


                        jQuery("#km-loader").show();


                        km_ajax_request = jQuery.ajax({

                            type: "POST",

                            async: false,

                            url: kmcl.ajaxurl,

                            data: {

                                'action': 'km_get_lens_type',

                                'left': left_dioptre,

                                'right': right_dioptre,

                                '_wpnonce': kmcl.kmcl_nonce,

                                'category': jQuery('.lenses-category:checked').val()

                            },


                            success: function (response) {



                                if (response != '') {

                                    jQuery("#section-content-lens-type").html(response);

                                    returndata = true;

                                   // jQuery("#km-loader").hide();

                                    return true;

                                } else {

                                    returndata = false;

                                   // jQuery("#km-loader").hide();

                                    return false;

                                }



                            }
                            ,
                            complete: function () {

                                jQuery("#km-loader").hide();
                            }


                        });

                    }


                }


                if (currentIndex == 3 && newIndex == 4) {


                    if (form.valid()) {


                        if (km_ajax_request) {

                            km_ajax_request.abort();

                        }

                        jQuery("#km-loader").show();

                        km_ajax_request = jQuery.ajax({

                            type: "POST",

                            async: false,

                            url: kmcl.ajaxurl,

                            data: {

                                'action': 'km_get_custom_lens',

                                '_wpnonce': kmcl.kmcl_nonce,

                                'category': jQuery('.lenses-category:checked').val(),

                                'type': jQuery('.lenses-types:checked').val()

                            },

                            success: function (response) {

                                //

                                if (response != '') {

                                    jQuery("#section-content-custom-lens").html(response);

                                    returndata = true;

                                } else {

                                    returndata = false;

                                }


                            }
                            ,
                            complete: function () {

                                jQuery("#km-loader").hide();
                            }


                        });

                    } else {

                        return false;

                    }

                }

                var formvalid = form.valid();

                if(jQuery("#skip_step").val() == 'true'){
                    formvalid = true;
                }

                if (returndata && formvalid) {

                    return true;

                } else {

                    return false

                }

                //return true;

            },
            onFinishing: function (event, currentIndex) {

                form.validate().settings.ignore = ":disabled,:hidden";



                if (form.valid()) {


                    if (km_ajax_request) {

                        km_ajax_request.abort();

                    }

                    jQuery("#km-loader").show();

                    var fd = new FormData();
                    var file = jQuery("#section-content-upload-prescription").find('input[type="file"]');

                    console.log(file[0].files);
                    var individual_file = file[0].files[0];
                    fd.append("file", individual_file);
                    fd.append("data", jQuery('form.cart').serialize());
                    fd.append('action', 'km_add_to_cart');
                    fd.append('_wpnonce', kmcl.kmcl_nonce);

                    console.log(fd);

                    km_ajax_request = jQuery.ajax({
                        type: "POST",
                        async: false,
                        url: kmcl.ajaxurl,
                        data: fd,
                        contentType: false,
                        processData: false,

                        success: function (obj) {
                            var response = JSON.parse(obj);
                            if (response.response == 'success') {

                                window.location.replace(response.url);

                                returndata = true;

                            } else {



                                returndata = false;

                            }


                        },
                        complete: function () {

                           jQuery("#km-loader").hide();
                        }

                    });

                }

                //return form.valid();

//                return true;

            },

            onFinished: function (event, currentIndex) {

                alert("Submitted!");

            },

            onInit: function () {

                var targetElement = jQuery('#main form.cart');

                var appendElement = jQuery('#main #option-wrap');


                targetElement.append(appendElement);


                appendElement.hide();

                appendElement.find('img').hide();

                appendElement.find('img:first-of-type').show();

                var close = appendElement.find('#cross');

                close.click(function () {

                    appendElement.fadeToggle();

                });

                jQuery('.single_add_to_lens').click(function (event) {


                    if (!jQuery(this).hasClass('disabled')) {

                        event.preventDefault();

                        appendElement.fadeToggle();

                        jQuery(".onepd").parent().hide();

                        jQuery(".twopd").parent().show();

                        /* Scroll to up section*/


                        jQuery('html,body').animate({

                                scrollTop: (appendElement.position().top)

                            },

                            'slow');


                    }


                });


                var radioBox = jQuery('#main #option-wrap').find('.radio-button-cont');

                jQuery(radioBox).on('click', function () {

                    radioBox.removeClass('radio-active');

                    jQuery(this).addClass('radio-active');

                })

                var checkradioBox = jQuery('#main #option-wrap').find('.section-content-lens-category');
                jQuery(checkradioBox).on('click', function () {
                    var index = jQuery(this).find('label').attr('data-index');
                    jQuery('.side-view').hide();
                    jQuery('#side-view' + index).show();
                })


                /* Select Option */

                /* Create the new select*/

                var select = jQuery('.fancy-select');

                /*

                    jQuery('.fancy-select').each(function () {

                        var select = jQuery(this);

                        var selectOption = select.find('option');

                        select.wrap('<div class="newSelect"></div>');

                        select.parent('.newSelect').prepend('<div class="selectedOption">0.00</div>');

                        select.parent('.newSelect').prepend('<div class="newOptions"></div>');



                        selectOption.each(function () {

                            var optionContents = jQuery(this).html();

                            var optionValue = jQuery(this).attr('value');

                            select.siblings('.newOptions').append('<div class="newOption" data-value="' + optionValue + '">' + optionContents + '</div>')

                        });



                        var parentObj = jQuery(this).parent('.newSelect');



                        select.parent('.newSelect').on('click', function () {

                            jQuery(this).addClass('clicked');

                        })



                        select.parent('.newSelect').on('mouseleave', function () {

                            jQuery(this).removeClass('clicked');

                        });



                        var newOption = select.siblings('.newOptions').children();





                        newOption.on('mousedown', function () {

                            var newValue = jQuery(this).attr('data-value');



                            jQuery(this).parent().siblings('.selectedOption').html(newValue).addClass('selected');

                            // update the actual input

                            selectOption.each(function () {

                                var optionValue = jQuery(this).attr('value');



                                if (newValue == optionValue) {

                                    jQuery(this).prop('selected', true);

                                } else {

                                    jQuery(this).prop('selected', false);

                                }

                            })

                            select.parent('.newSelect').trigger('mouseleave')



                        });



                    });*/


            }

        });

        form.validate({

            onkeyup: false,

            ignore: [],

            errorPlacement: function errorPlacement(error, element) {

                element.siblings('label').after(error);

            },

            rules: {

                'lenses-category': 'required',

                'lenses-types': 'required',

                'post': 'required',

                'upload_image':'required'

            }

        });

        jQuery(document).on('click', ".add_to_vendor_cart", function (e) {
            e.preventDefault();

            jQuery("input[name=p_id]").val(jQuery(this).data('id'));
            (jQuery("input[name=p_id]").val());

            popupwizard.steps('finish');

           jQuery(".btn-finish").trigger('click');


        })

        jQuery(document).on('click', ".btn-next", function (e) {

            e.preventDefault()

            popupwizard.steps('next');

        })
        jQuery(document).on('change', ".next-step", function (e) {

            popupwizard.steps('next');

        })


        jQuery(document).on('click', ".btn-goto", function (e) {
            e.preventDefault()
            jQuery("#skip_step").val('true');


            popupwizard.steps('next');

        })
        jQuery(document).on('click', ".notskip", function (e) {
            e.preventDefault()
            jQuery("#skip_step").val('false');

            popupwizard.steps('next');
        })


        jQuery(document).on('click', ".btn-back", function (e) {

            e.preventDefault()

            popupwizard.steps('previous');

        })

        jQuery(document).on('click', ".btn-finish", function (e) {


            e.preventDefault()

            popupwizard.steps('finish');

        })


    });


</script>


<style>

    /* Table settings */

    #select-option-tab {

        position: relative;

    }

    #section-content-lens-type, #section-content-custom-lens {

        min-height: 98px;

    }

    .radio-button-cont .price.strike {

        right: 80px;

        text-decoration: line-through;

    }

    #km-loader {

        display: none;

        position: absolute;

        top: 0;

        bottom: 0;

        width: 100%;

        background: rgba(0, 0, 0, 0.3);

        z-index: 9;

    }

    .content .fa-spin {

        top: 50%;

        position: absolute;

        left: 50%;

        color: #000;

        font-size: 35px;

        margin-left: -24px;

        margin-top: -24px;

    }

    .onepd {

        margin-bottom: 10px;

    }

    .table {

        background: #f7f7f7;

        padding: 30px 20px 20px 15px;

    }

    .table-row:after, .table-row:before, .table:after, .table:before {

        content: '';

        content: " ";

        display: table;

    }

    .table-row:after, .table:after {

        clear: both;

    }

    .heading-row {

        font-weight: bold;

    }

    .table-row .td-element:last-child {

        margin-left: 20px;

    }

    .table .fa-question-circle {

        color: #3369e8;

        font-size: 16px;

    }

    .table-row .td-element:first-child {

        -webkit-flex: 0 1 15%;

        -ms-flex: 0 1 15%;

        flex: 0 1 15%;

        -webkit-align-self: flex-end;

        -ms-flex-item-align: end;

        align-self: flex-end;

        padding-bottom: 18px;

    }

    .table-row .td-element {

        text-align: center;

        -webkit-flex: 0 1 20%;

        -ms-flex: 0 1 20%;

        flex: 0 1 20%;

    }

    .table-col-2 .td-element {

        margin-left: 20px;

        background: #ebebeb;

        padding: 7px 18px 40px;

        border-radius: 3px;

        -webkit-flex: 1 1 16%;

        -ms-flex: 1 1 16%;

        flex: 1 1 16%;

    }

    .table-col-10 {

        width: 70%;

        float: left;

    }

    .table-col-2 {

        float: left;

        width: 30%;

    }

    .table-row .table-col {

        display: flex;

        display: -webkit-flex;

        flex-direction: row;

        -webkit-flex-direction: row;

        flex-grow: 0;

        -webkit-flex-grow: 0;

        flex-wrap: wrap;

        -webkit-flex-wrap: wrap;

        margin-bottom: 18px;

    }

    .clearfix:after {

        content: '';

        display: block;

        clear: both;

    }

    .pdno-check {

        font-size: 12px;

        color: #000;

        margin-top: 4px;

        display: inline-block;

        text-decoration: underline;

    }

    .table-body .table-col-2 .td-element {

        padding-top: 20px;

        padding-bottom: 25px;

    }

    .radio-button-cont {

        text-align: left;

        padding: 22px 20px;

        border: 1px solid #e5e5e5;

        position: relative;

        margin-bottom: 15px;

    }

    .option-swatch .title span {

        font-size: 14px;

        font-family: "Open Sans", sans-serif;

    }

    .woocommerce-tabs {

        position: relative;

        z-index: 99;

    }

    .radio-button-cont.radio-active .price {

        color: #3d71e9;

    }

    .radio-button-cont .price {

        position: absolute;

        right: 18px;

        top: 20px;

        font-weight: bold;

        color: #c4c8ce;

        font-size: 16px;

    }

    .radio-text p {

        margin-bottom: 0;

        color: #334b60;

        font-size: 15px;

    }

    .radio-text h5 {

        font-size: 16px;

        font-weight: 700;

        font-family: "Open Sans", sans-serif;

        margin-bottom: 3px;

    }

    .option-swatch #cross {

        text-align: right;

        display: inline-block;

        position: absolute;

        top: 15px;

        right: 0;

        font-size: 20px;

        cursor: pointer;

    }

    .option-swatch {

        padding-top: 80px;

        display: none;

        position: absolute;

        width: 100%;

        height: 100%;

        background: #fff;

        z-index: 99;

        top: 0;

    }

    .option-swatch .radio-text {

        padding-left: 30px;

    }

    .fancy-select select {

        font-size: 18px;

    }

    .fancy-select {

        padding: 18px 9px;

        margin: 0;

        width: 100%;

    }

    .table-col .td-element:nth-child(2) {

        -webkit-border-radius: 4px 0 0 4px;

        border-radius: 4px 0 0 4px;

    }

    .select-wrap::after {

        content: '\f078';

        position: absolute;

        top: 34%;

        right: 9px;

        font-family: fontawesome;

        color: #3369e8;

    }

    .select-wrap {

        position: relative;

    }

    .select-wrap select {

        -webkit-appearance: none;

        -moz-appearance: none;

        appearance: none;

    }

    .select-wrap select::-ms-expand {

        display: none;

    }

    .td-element select {

        -webkit-border-radius: 4px;

        border-radius: 4px;

        border-color: #e1e1e1;

        font-size: 18px;

    }

    .table-col .td-element:nth-child(2) select {

        -webkit-border-radius: 4px 0 0 4px;

        border-radius: 4px 0 0 4px;

    }

    .table-col .td-element select {

        border-radius: 0;

        border-right: none;

    }

    .table-col .td-element:nth-child(4) select {

        border-right: 1px solid #e1e1e1;

        -webkit-border-radius: 0 4px 4px 0;

        border-radius: 0 4px 4px 0;

    }

    .table-col .td-element:last-child select {

        border-right: 1px solid #e1e1e1;

        -webkit-border-radius: 4px;

        border-radius: 4px;

    }

    .table-body .newSelect {

        position: relative;

        display: block;

    }

    .table-body .newSelect:hover {

        height: auto;

    }

    .table-body .td-element + .td-element .newSelect .selectedOption {

        border-radius: 0;

    }

    .table-body .newSelect .selectedOption {

        background: white;

        padding: 18px 14px;

        font-size: 18px;

        line-height: 18px;

        height: 58px;

        border: 1px solid #e1e1e1;

        color: #000;

        cursor: pointer;

        position: relative;

        border-radius: 4px 0px 0 4px;

        border-right: none;

        margin-bottom: 9px;

        text-align: left;

    }

    .table-body .td-element:nth-child(2) .newSelect .selectedOption {

        border-radius: 4px 0px 0 4px;

    }

    .table-col-10 .td-element:nth-last-child(2) .newSelect .selectedOption {

        border-right: 1px solid #e1e1e1;

        border-radius: 3px 3px 3px 3px;

    }

    .table-body .td-element:last-child .newSelect .selectedOption {

        border-right: 1px solid #e1e1e1;

        border-radius: 3px 3px 3px 3px;

    }

    .table-body .newSelect .selectedOption:after {

        font-family: FontAwesome;

        content: '\f078';

        position: absolute;

        right: 11px;

        color: #3369e8;

        font-size: 13px;

    }

    .table-row .title {

        padding-bottom: 5px;

    }

    .table-body .newSelect .selectedOption.selected {

        color: #134b4e;

    }

    .table-body .newSelect .newOptions {

        position: absolute;

        width: 100%;

        top: 100%;

        z-index: 9999;

    }

    .table-body .newSelect .newOptions .newOption {

        display: none;

        top: 0;

        left: 0;

        font-size: 16px;

        line-height: 18px;

        height: 37px;

        padding: 8px;

        background: #fff;

        color: #020202;

        cursor: pointer;

        border: 1px solid #e1e1e1;

        border-top: 0;

    }

    .table-body .newSelect .newOptions .newOption:hover {

        background: #3369e8;

        color: #fff;

    }

    .table-body .newSelect.clicked .newOption {

        display: block;

    }

    .table-body .newSelect.closed .newOption {

        display: none;

    }

    .option-swatch .steps li.resp-tab-active {

        color: #3369e8;

    }

    .option-swatch .steps li {

        font-weight: 600;

        margin-right: 20px;

        padding-left: 0 !important;

        padding-right: 0 !important;

    }

    .option-swatch .steps li:first-child {

        padding-left: 0 !important;

    }

    .option-swatch [type="radio"]:checked,
    .option-swatch [type="radio"]:not(:checked) {

        position: absolute;

        left: -9999px;

    }

    .option-swatch [type="radio"]:checked + label,
    .option-swatch [type="radio"]:not(:checked) + label {

        position: absolute;

        padding-left: 28px;

        cursor: pointer;

        line-height: 20px;

        display: inline-block;

        color: #c4c8ce;

        top: 0px;

        bottom: 0;

        left: 0px;

        right: 0;

    }

    .option-swatch [type="radio"]:checked + label:before,
    .option-swatch [type="radio"]:not(:checked) + label:before {

        content: '\f058';

        position: absolute;

        left: 15px;

        top: 22px;

        width: 18px;

        height: 18px;

        border-radius: 100%;

        background: #fff;

        font: normal normal normal 14px/1 FontAwesome;

        font-size: 24px;

    }

    .option-swatch [type="radio"]:checked + label:before, .radio-active label:before {

        color: #3d71e9;

    }

    .radio-button-cont.radio-active:after,
    .radio-button-cont.radio-active:before {

        content: '';

        display: block;

        position: absolute;

        right: 100%;

        width: 0;

        height: 0;

        border-style: solid;

    }

    .radio-button-cont.radio-active:after {

        top: 50%;

        border-color: transparent #fff transparent transparent;

        border-width: 11px;

        margin-top: -18px;

    }

    .radio-button-cont.radio-active:before {

        top: 50%;

        border-color: transparent #e5e5e5 transparent transparent;

        border-width: 13px;

        margin-top: -20px;

    }

    .option-eye-sight {

        background: #f7f7f7;

    }

    .button-container a {

        display: block;

        margin-top: 10px;

    }

    @media (min-width: 1300px) and (max-width: 1440px) {

        .table-col-10 {

            width: 75%;

            float: left;

        }

        .table-col-2 {

            float: left;

            width: 25%;

        }

    }

    .table .add-no {

        display: none;

    }

    @media (max-width: 1299px) {

        .table-col-10 {

            width: 100%;

            float: left;

        }

        .table-col-2 {

            float: left;

            width: 100%;

        }

    }

    @media (max-width: 991px) {

        #option-wrap .col-md-6 img {

            display: block;

            margin: 0 auto 30px;

            max-width: 300px;

            width: 100%;

        }

    }

    @media (max-width: 767px) {

        .table-col-2 .select-wrap {

            max-width: 82px;

            margin: 0 auto;

        }

        #option-wrap .col-md-6 img {

            max-width: 320px;

        }

        .fancy-select {

            padding: 10px 9px;

        }

        .resp-tabs-list li {

            padding: 5px 20px 5px 20px !important;

        }

        .table {
            padding: 20px 10px 20px 10px;
        }

        .table-row .table-col {

            margin-bottom: 10px;

        }

        .table-row .td-element:first-child {

            padding-bottom: 6px;

        }

        .table-col-2 .td-element:last-child {

            margin-left: 0;

            padding-bottom: 18px;

        }

        .wizard > .steps {
            margin-bottom: 15px;
        }

    }

    @media (max-width: 480px) {
        #option-wrap.option-swatch {
            max-height: 670px;
        }

        .radio-text h5 {
            font-size: 14px;
        }

        .radio-text p {
            font-size: 14px;
        }

        .table-col-10 {

            overflow-x: auto;

        }

        .table-row .table-col {
            width: 390px;
            flex-wrap: nowrap;
            -webkit-flex-wrap: nowrap;
        }
    }
</style>

