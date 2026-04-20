function wms_open_component(id)
{
    let $html_loading = '<div id="circularG"><div id="circularG_1" class="circularG"></div><div id="circularG_2" class="circularG"></div><div id="circularG_3" class="circularG"></div><div id="circularG_4" class="circularG"></div><div id="circularG_5" class="circularG"></div><div id="circularG_6" class="circularG"></div><div id="circularG_7" class="circularG"></div><div id="circularG_8" class="circularG"></div></div>'

    jQuery(".wms-view-component").removeClass('wms-view-component-none' );
    jQuery(".wms-view-component").addClass('wms-view-component-active' );
    jQuery(".wms-view-component").html($html_loading );


    jQuery.ajax({
        url: "/wp-admin/admin-ajax.php",
        method: 'post',
        data: {
            action: 'wms_load_component',
            id:id

        },
        success: function (response) {
            if(response.success){
                jQuery('.wms-view-component').html(response.data);
            }
        }
    });

}

function wms_close_component()
{
    jQuery(".wms-view-component").removeClass('wms-view-component-active' );
    jQuery(".wms-view-component").addClass('wms-view-component-none' );
    jQuery(".wms-view-component").html('');

}


