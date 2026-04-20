setInterval("wms_monitor()", 15000);

function wms_monitor() {
        jQuery.ajax({
            url: wcstoresMsCoreAdmin.ajax_url,
            method: 'post',
            data: {
                action: 'wms_monitor',
                wms_nonce:wms_nonce
            },
            success: function (response) {
                jQuery('#wms-main-monitor').html(response.substr(response.length - 1, 1) === '0' ? response.substr(0, response.length - 1) : response);
            }
        });
}

function wms_load_button_sync(classname) {
    html = '<div id="circularG"><div id="circularG_1" class="circularG"></div><div id="circularG_2" class="circularG"></div><div id="circularG_3" class="circularG"></div><div id="circularG_4" class="circularG"></div><div id="circularG_5" class="circularG"></div><div id="circularG_6" class="circularG"></div><div id="circularG_7" class="circularG"></div><div id="circularG_8" class="circularG"></div></div>'
    jQuery(classname).attr('disabled', 'disabled');
    jQuery(classname).html(html);

}

function wms_stop_button_sync(classname, html) {
    jQuery(classname).removeAttr('disabled');
    jQuery(classname).html(html);

}


function wms_start_assortment() {
    wms_load_button_sync('.loadproduct');
    jQuery.ajax({
        url: wcstoresMsCoreAdmin.rest_start_sync_product_url,
        method: 'post',
        data: {
            action: 'wms-load-start-product-syn',
            start: 'start',
            wms_nonce:wms_nonce
        },
        success: function (response) {
            jQuery('#product-monitor-id').html(response.substr(response.length - 1, 1) === '0' ? response.substr(0, response.length - 1) : response);
        }
    });

}


function wms_start_counterparty() {
    wms_load_button_sync('.loadcounterparty');
    jQuery.ajax({
        url: wcstoresMsCoreAdmin.ajax_url,
        method: 'post',
        data: {
            action: 'wms-load-start-counterparty-syn',
            start: 'start',
            wms_nonce:wms_nonce
        }
    });

}


function wms_start_stock() {
    wms_load_button_sync(".loadstock");

    jQuery.ajax({
        url: wcstoresMsCoreAdmin.ajax_url,
        method: 'post',
        data: {
            action: 'wms-load-start-stock-syn',
            start: 'start',
            wms_nonce:wms_nonce
        },
        success: function (response) {
            jQuery('#stock-stock-id').html(response.substr(response.length - 1, 1) === '0' ? response.substr(0, response.length - 1) : response);
        }
    });

}


function wms_stopsync() {
    jQuery.ajax({
        url: wcstoresMsCoreAdmin.ajax_url,
        method: 'post',
        data: {
            action: 'wms_stop_sync',
            start: 'stop',
            wms_nonce:wms_nonce
        },
    });
}

function wms_startsync() {
    jQuery.ajax({
        url: wcstoresMsCoreAdmin.ajax_url,
        method: 'post',
        data: {
            action: 'wms_start_sync',
            start: 'start',
            wms_nonce:wms_nonce
        },
    });

}


function wms_rsync() {
    jQuery.ajax({
        url: wcstoresMsCoreAdmin.ajax_url,
        method: 'post',
        data: {
            action: 'wms_r_sync',
            start: 'res',
            wms_nonce:wms_nonce
        },
    });

}


function wms_tab() {
    //for bootstrap 3 use 'shown.bs.tab' instead of 'shown' in the next line
    jQuery('a[data-toggle="pill"]').on('click', function (e) {
        //save the latest tab; use cookies if you like 'em better:
        localStorage.setItem('lastTab', jQuery(e.target).attr('href'));
    });

    //go to the latest tab, if it exists:
    var lastTab = localStorage.getItem('lastTab');

    if (lastTab) {
        jQuery('a[href="' + lastTab + '"]').click();
    }
}


function wms_button_add_style() {
    jQuery('.submit input[type="submit"]').removeClass('button');
}


function wms_add_style_states() {
    jQuery.ajax({
        url: wcstoresMsCoreAdmin.ajax_url,
        method: 'post',
        data: {
            action: 'wms_add_style_states',
        },
        success: function (response) {
            jQuery('#add-states').html(response.substr(response.length - 1, 1) === '0' ? response.substr(0, response.length - 1) : response);
        }
    });
}

