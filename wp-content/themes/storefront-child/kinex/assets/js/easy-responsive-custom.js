jQuery(document).ready(function ($) {
    $('#horizontalTab').easyResponsiveTabs({
        type: 'default', //Types: default, vertical, accordion
        width: 'auto', //auto or any width like 600px
        fit: true,   // 100% fit in a container
        closed: 'accordion', // Start closed if in accordion view
        activate: function (event) { // Callback function if tab is switched
            var $tab = $(this);
            var $info = $('#tabInfo');
            var $name = $('span', $info);
            $name.text($tab.text());
            $info.show();
        }
    });

    $('#filterTabs').easyResponsiveTabs({
        type: 'default', //Types: default, vertical, accordion
        width: 'auto', //auto or any width like 600px
        fit: true,   // 100% fit in a container
        closed: 'accordion', // Start closed if in accordion view
        activate: function (event) { // Callback function if tab is switched

        }
    });
});

jQuery(document).ready(function () {

    jQuery('.wcmp_vendor_dashboard_content input,.wcmp_vendor_dashboard_content textarea,.wcmp_vendor_dashboard_content select,.wcmp_vendor_dashboard_content button').removeAttr('readonly')
    jQuery('.menu_toggle').click(function () {
        jQuery(".quick-menu").toggle();
    });
    jQuery('.handheld-menu').click(function () {
        if (jQuery(this).find('i').hasClass('fa-angle-down')) {
            jQuery(this).find('i').removeClass('fa-angle-down')
            jQuery(this).find('i').addClass('fa-angle-up')
        } else {
            jQuery(this).find('i').removeClass('fa-angle-up')
            jQuery(this).find('i').addClass('fa-angle-down')
        }

        jQuery(".handheld-wrap").slideToggle(300);
    });
});
