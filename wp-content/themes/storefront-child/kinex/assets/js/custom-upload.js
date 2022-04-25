// (function ($) {
//
//     $.wpMediaUploader = function (options) {
//
//         var settings = $.extend({
//             removeitem: 'remove_mfi_image',
//             name: 'mfi_image',
//             appenditem: 'mfi_image_item',
//             appendobject: '#mfi_images',
//             target: '.smartcat-multiple-uploader', // The class wrapping the textbox
//             uploaderTitle: 'Select or upload image', // The title of the media upload popup
//             uploaderButton: 'Set image', // the text of the button in the media upload popup
//             multiple: true, // Allow the user to select multiple images
//             buttonText: 'Upload image', // The text of the upload button
//             buttonClass: '.smartcat-multiple-upload', // the class of the upload button
//             previewSize: '200px', // The preview image size
//             modal: false, // is the upload button within a bootstrap modal ?
//             buttonStyle: { // style the button
//                 color: '#fff',
//                 background: '#3bafda',
//                 fontSize: '16px',
//                 padding: '10px 8px',
//             },
//
//
//         }, options);
//
//         $(settings.target).append('<li class="not-sort">\n\
//                                         <a href="#" class="' + settings.buttonClass.replace('.', '') + '">' + settings.buttonText + '</a>\n\
//                                            </li>');
//
//         $(settings.buttonClass).css(settings.buttonStyle);
//
//         $('body').on('click', settings.buttonClass, function (e) {
//
//             e.preventDefault();
//             var selector = $(this).parent(settings.target);
//             var custom_uploader =  wp.media.frames.file_frame = wp.media({
//                  title: settings.uploaderTitle,
//                 button: {
//                     text: settings.uploaderButton
//                 },
//                 // library: {
//                 //     type: 'image' // limits the frame to show only images
//                 // },
//                 multiple: settings.multiple
//             }).on('select', function () {
//                 var attachment = custom_uploader.state().get('selection').toJSON();
//
//                 console.log(attachment);
//
//                 for (var i = 0; i < attachment.length; i++) {
//                     if (settings.multiple) {
//                         $(settings.appendobject).append('<li class="' + settings.appenditem +
//                             '" style="background-image: url(' + attachment[i].url + ');" >\n\
//                             <input type="hidden" name="' + settings.name + '[]" value="' + attachment[i].id + '" /> \n\
//                             <span class="' + settings.removeitem + '">X</span>\n\
//                         </li>');
//                     } else {
//                         $(settings.appendobject).children().remove();
//                         $(settings.appendobject).append('<li class="' + settings.appenditem +
//                             '" style="background-image: url(' + attachment[i].url + ');" >\n\
//                             <input type="hidden" name="' + settings.name + '" value="' + attachment[i].id + '" /> \n\
//                             <span class="' + settings.removeitem + '">X</span>\n\
//                         </li>');
//                     }
//
//
//                 }
//
//                 if (settings.modal) {
//                     $('.modal').css('overflowY', 'auto');
//                 }
//
//
//             }).open();
//
//             console.log(custom_uploader);
//
//         });
//
//     };
//
// })(jQuery);
//
//
// jQuery(document).ready(function ($) {
//
//     $.wpMediaUploader({
//         removeitem: 'remove_vendor_image_i',
//         name: 'vendor_image_i',
//         appenditem: 'vendor_image_i_item',
//         appendobject: '#vendor_image_i',
//         target: '.vendor_image_i-uploader', // The class wrapping the textbox
//         uploaderTitle: 'Select or upload Owner', // The title of the media upload popup
//         uploaderButton: 'Set image', // the text of the button in the media upload popup
//         multiple: false, // Allow the user to select multiple images
//         buttonText: 'Upload Owner Image', // The text of the upload button
//         buttonClass: '.vendor_image_i-upload', // the class of the upload button
//         previewSize: '200px', // The preview image size
//         modal: false, // is the upload button within a bootstrap modal ?
//         buttonStyle: { // style the button
//             color: '#fff',
//             background: '#3bafda',
//             fontSize: '16px',
//             padding: '10px 8px',
//         },
//
//     });
//
//
//     $('#vendor_image_i').on('click', 'li.vendor_image_i_item .remove_vendor_image_i', function () {
//
//         $(this).parent().hide('slow', function () {
//             $(this).remove();
//         });
//
//     });
//
//     $.wpMediaUploader({
//         removeitem: 'remove_vendor_banner_i',
//         name: 'vendor_banner_i',
//         appenditem: 'vendor_banner_i_item',
//         appendobject: '#vendor_banner_i',
//         target: '.vendor_banner_i-uploader', // The class wrapping the textbox
//         uploaderTitle: 'Select or upload Banner', // The title of the media upload popup
//         uploaderButton: 'Set Banner', // the text of the button in the media upload popup
//         multiple: false, // Allow the user to select multiple images
//         buttonText: 'Upload Banner', // The text of the upload button
//         buttonClass: '.vendor_banner_i-upload', // the class of the upload button
//         previewSize: '200px', // The preview image size
//         modal: false, // is the upload button within a bootstrap modal ?
//         buttonStyle: { // style the button
//             color: '#fff',
//             background: '#3bafda',
//             fontSize: '16px',
//             padding: '10px 8px',
//         },
//     });
//
//
//     $('#vendor_banner_i').on('click', 'li.vendor_banner_i_item .remove_vendor_banner_i', function () {
//
//         $(this).parent().hide('slow', function () {
//             $(this).remove();
//         });
//
//     });
//
//
//     $.wpMediaUploader({
//         removeitem: 'remove_vendor_fshop_i',
//         name: 'vendor_fshop_i',
//         appenditem: 'vendor_fshop_i_item',
//         appendobject: '#vendor_fshop_i',
//         target: '.vendor_fshop_i-uploader', // The class wrapping the textbox
//         uploaderTitle: 'Select or upload owner image', // The title of the media upload popup
//         uploaderButton: 'Set owner image', // the text of the button in the media upload popup
//         multiple: false, // Allow the user to select multiple images
//         buttonText: 'Upload owner Image', // The text of the upload button
//         buttonClass: '.vendor_fshop_i-upload', // the class of the upload button
//         previewSize: '200px', // The preview image size
//         modal: false, // is the upload button within a bootstrap modal ?
//         buttonStyle: { // style the button
//             color: '#fff',
//             background: '#3bafda',
//             fontSize: '16px',
//             padding: '10px 8px',
//         },
//     });
//
//
//     $('#shop_images').on('click', 'li.vendor_fshop_i_item .shop_vendor_fshop_i', function () {
//
//         $(this).parent().hide('slow', function () {
//             $(this).remove();
//         });
//
//     });
//
//
// });
    jQuery(document).on("click",".next-click-btn",function($){
        if(jQuery(".first").hasClass('current')){
            jQuery(".steps-opt").addClass("first-step");
        }
    });
    
    jQuery(document).on("click",".next-click",function($){
         jQuery(".steps-opt").removeClass("first-step"); 
    });
    
    
    jQuery(document).on("click",".steps li a",function(){
       if(jQuery(".first").hasClass("current")){
           jQuery(".steps-opt").addClass("first-step");
           jQuery(".steps-opt").removeClass("another-step");
           
       }else{
           jQuery(".steps-opt").removeClass("first-step");
           jQuery(".steps-opt").addClass("another-step");
       }
    });
    
  
  /* function backfire(){
    jQuery('#select-option-tab-t-1').click();
}
    
function backevent(backevent,callback){
    jQuery(backevent).each(function() {
        //console.log(jQuery(this).val());
        if(jQuery(this).val() == ""){
            jQuery(this).addClass("error");
    		setTimeout( function(){
            	jQuery('#select-option-tab-t-1').click();
          	},150)
            callback();
            }
        
    })
}
jQuery(document).on("click",".move-next-to-second",function(e){
	backevent('.fa-select',backfire);
}); */


// jQuery(document).on("click",".on-image-upload",function(e){
// 	backevent1('.fa-select',backfireto);
// });

// function backfireto(){
//     jQuery("#select-option-tab-t-2").click();
// }
// function backevent1(){
//   jQuery(".km_upload_prescription")
// }
// km_upload_prescription
    