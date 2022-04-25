jQuery(document).ready(function ($) {
    // fav_button_warp


    $(document).on('click', '.simple_add_to_shop_favourites', function (e) {
        e.preventDefault();
        var prod_id = $(this).data().shopid;
        if (isNaN(prod_id)) {
            return;
        }
        prod_id = parseInt(prod_id);

        var $this_button = $(this);
        udata = {
            prod_id: prod_id,
            action: 'km_shop_add_to_favourites',
            km_favourites_product_nonce: km_favourites_nonce.km_favourites_product_nonce
        }
        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            data: udata,
            url: fajax.ajaxurl,
            success: function (response) {

                var $this_messsage = $this_button.closest('#fav_button_warp').parent().find('.simple_message');

                $this_messsage.text(response.msg);
                $this_messsage.fadeIn();
                setTimeout(function () {
                    $this_messsage.fadeOut();
                }, 4000);
                if (response.response == 'success') {
                    var $buttonwrap = $this_button.closest('#fav_button_warp');
                    $buttonwrap.html(response.btn);
                }


            }
        });

    });
    $(document).on('click', '.simple-remove-from-shop-favourites', function (e) {
        e.preventDefault();
        var prod_id = $(this).data().shopid;
        if (isNaN(prod_id)) {
            return;
        }
        var $this_button = $(this);
        prod_id = parseInt(prod_id);
        udata = {
            prod_id: prod_id,
            action: 'km_ajax_remove_from_shop_favourites',
            km_favourites_product_nonce: km_favourites_nonce.km_favourites_product_nonce
        }

        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            data: udata,
            url: fajax.ajaxurl,
            success: function (response) {

                var $this_messsage = $this_button.closest('#fav_button_warp').parent().find('.simple_message');
                $this_messsage.text(response.msg);
                $this_messsage.fadeIn();
                setTimeout(function () {
                    $this_messsage.fadeOut();
                }, 4000);
                if (response.response == 'success') {
                    var $buttonwrap = $this_button.closest('#fav_button_warp');
                    $buttonwrap.html(response.btn);

                }


            }
        });

    });



    $(document).on('click', '.simple_add_to_favourites', function (e) {

        e.preventDefault();
        var prod_id = $(this).data().productid;
        if (isNaN(prod_id)) {
            return;
        }
        prod_id = parseInt(prod_id);

        var $this_button = $(this);
        udata = {
            prod_id: prod_id,
            action: 'km_product_add_to_favourites',
            km_favourites_product_nonce: km_favourites_nonce.km_favourites_product_nonce
        }
        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            data: udata,
            url: fajax.ajaxurl,
            success: function (response) {

                var $this_messsage = $this_button.closest('#fav_button_warp').parent().find('.simple_message');

                $this_messsage.text(response.msg);
                $this_messsage.fadeIn();
                setTimeout(function () {
                    $this_messsage.fadeOut();
                }, 4000);
                if (response.response == 'success') {
                    var $buttonwrap = $this_button.closest('#fav_button_warp');
                    $buttonwrap.html(response.btn);
                }


            }
        });

    });
    $(document).on('click', '.simple-remove-from-favourites', function (e) {
        e.preventDefault();
        var prod_id = $(this).data().productid;
        if (isNaN(prod_id)) {
            return;
        }
        var $this_button = $(this);
        prod_id = parseInt(prod_id);
        udata = {
            prod_id: prod_id,
            action: 'km_ajax_remove_from_favourites',
            km_favourites_product_nonce: km_favourites_nonce.km_favourites_product_nonce
        }

        jQuery.ajax({
            type: 'POST',
            dataType: "json",
            data: udata,
            url: fajax.ajaxurl,
            success: function (response) {

                var $this_messsage = $this_button.closest('#fav_button_warp').parent().find('.simple_message');
                $this_messsage.text(response.msg);
                $this_messsage.fadeIn();
                setTimeout(function () {
                    $this_messsage.fadeOut();
                }, 4000);
                if (response.response == 'success') {
                    var $buttonwrap = $this_button.closest('#fav_button_warp');
                    $buttonwrap.html(response.btn);
                    console.log($buttonwrap.parents('li'));
                   $buttonwrap.parents('li').remove();
                }


            }
        });

    });




    if ($('#simple_favourites_display').length != 0) {
        var max_height = 0;
        $('ul.products li.product').each(function () {
            max_height = $(this).height() > max_height ? $(this).height() : max_height;
        });
        $('ul.products li.product').height(max_height);
    }

})
;