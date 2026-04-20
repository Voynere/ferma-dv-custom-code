jQuery(function ($) {

    $(document).ready(function () {

        $(document).on('click', '.wcsms-send-order__js-button', function () {

            let $order_id = $(this).data('order_id')
                wms_send_order($order_id)
            });

    })

    function wms_send_order(order_id) {

        let $html = '<div id="circularG"><div id="circularG_1" class="circularG"></div><div id="circularG_2" class="circularG"></div><div id="circularG_3" class="circularG"></div><div id="circularG_4" class="circularG"></div><div id="circularG_5" class="circularG"></div><div id="circularG_6" class="circularG"></div><div id="circularG_7" class="circularG"></div><div id="circularG_8" class="circularG"></div></div>'
        jQuery('#wcsms-send-order-' + order_id).html($html);

        jQuery.ajax({
            url: wcstoresMsCoreAdmin.ajax_url,
            method: 'post',
            data: {
                action: 'wms_send_order',
                order_id: order_id,


            },
            success: function (response) {
                jQuery('#wcsms-send-order-' + order_id).html(response);
            }
        });

    }

});
