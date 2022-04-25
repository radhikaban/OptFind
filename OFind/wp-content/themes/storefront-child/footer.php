<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package storefront
 */

?>

</div><!-- .col-full -->
</div><!-- #content -->

<?php do_action('storefront_before_footer'); ?>
<footer id="colophon" class="site-footer" role="contentinfo">
    <div class="footer-top">
        <div class="col-full"><?php dynamic_sidebar('sidebar-footer-subscribe'); ?></div>
    </div>
    <div class="col-full">

        <?php
        /**
         * Functions hooked in to storefront_footer action
         *
         * @hooked storefront_footer_widgets - 10
         * @hooked storefront_credit         - 20
         */
        do_action('storefront_footer'); ?>

    </div><!-- .col-full -->
    <div class="footer-bottom">
        <div class="col-full"><?php dynamic_sidebar('sidebar-footer-copyright'); ?></div>
    </div>
</footer><!-- #colophon -->

<?php do_action('storefront_after_footer'); ?>

</div><!-- #page -->
<?php wp_footer(); ?>

<?php
@session_start();


if(!isset($_SESSION['lat'])):
   ?>



   <script type="text/javascript">



    var apiGeolocationSuccess = function(position) {

        km_ajax_request = jQuery.ajax({

            type: "POST",

            async: false,

            url: kmcl.ajaxurl,

            data: {

                'action': 'km_set_locations',

                'lat': position.coords.latitude,

                'lang': position.coords.longitude,

                '_wpnonce': kmcl.kmcl_nonce,

            },


            success: function (response) {

            }

        });

    };

    var tryAPIGeolocation = function() {
        jQuery.post( "https://www.googleapis.com/geolocation/v1/geolocate?key=AIzaSyBzC7lfTwmOkEThN3gnrs19Niznnx3Ys5k", function(success) {
            apiGeolocationSuccess({coords: {latitude: success.location.lat, longitude: success.location.lng}});
        })
        .fail(function(err) {
            console.log("API Geolocation error! \n\n"+err);
        });
    };

    var browserGeolocationSuccess = function(position) {

        console.log("Browser geolocation success!\n\nlat = " + position.coords.latitude + "\nlng = " + position.coords.longitude);
        apiGeolocationSuccess({coords: {latitude: position.coords.latitude,longitude:  position.coords.longitude}});
    };

    var browserGeolocationFail = function(error) {
      console.log(error);
      switch (error.code) {
        case error.TIMEOUT:
        console.log("Browser geolocation error !\n\nTimeout.");
        break;
        case error.PERMISSION_DENIED:
        if(error.message.indexOf("Only secure origins are allowed") == 0 || error.message.indexOf("User denied geolocation prompt") == 0) {
            tryAPIGeolocation();
        }
        break;
        case error.POSITION_UNAVAILABLE:
        console.log("Browser geolocation error !\n\nPosition unavailable.");
        break;
    }
};

var tryGeolocation = function() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            browserGeolocationSuccess,
            browserGeolocationFail,
            {maximumAge: 50000, timeout: 20000, enableHighAccuracy: true});
    }
};

tryGeolocation();

</script>
<?php endif; ?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        //jQuery('.simple_add_to_favourites:first').remove();
        jQuery("#select-option-tab #cyl-right option[value='0.00']").prop("selected", true);
        jQuery("#select-option-tab #sph-right option[value='0.00']").prop("selected", true);
        jQuery("#select-option-tab #cyl-left option[value='0.00']").prop("selected", true);
        jQuery("#select-option-tab #sph-left option[value='0.00']").prop("selected", true);

        // var testimonialElements = jQuery(".option-swatch");
        // for(var i=0; i<testimonialElements.length; i++){
        //     var element = testimonialElements.eq(i).addClass('l'+i);
        // }
        if(jQuery('.option-swatch').length > 1){
            jQuery('.option-swatch').eq(0).remove();
        }
    });
</script>
</body>
</html>