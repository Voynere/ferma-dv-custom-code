
function wms_clear_groop() {
    wms_load_button_sync('.wms-clear-groop');
    jQuery.ajax({
        url: "/wp-admin/admin-ajax.php",
        method: 'post',
        data: {
            action: 'wms_clear_groop',
            start: 'stop',


        },
        success: function (response) {
            jQuery('.wms-clear-groop').html(response.substr(response.length - 1, 1) === '0' ? response.substr(0, response.length - 1) : response);
            location.reload();
        }
    });
}


function wms_groop_ul(idparent, id) {

    var $resForm = jQuery('.' + idparent + '>legend');
    if ($resForm.length > 0) {
        // Да, такой элемент существует.
    } else {
        jQuery('.' + idparent).prepend('<legend>+</legend>');
    }

    jQuery('.' + idparent).append(jQuery('.' + id));
    jQuery('.' + id).addClass('closed');

}

jQuery(function () {
    var checkbox = jQuery("[data-wms-parent-id]");

    for (var i = 0; i < checkbox.length; i++) {
        var idparent = checkbox[i].getAttribute('data-wms-parent-id');

        if (idparent.length > 0) {
            var id = checkbox[i].id;
            wms_groop_ul(idparent, id)
        }
    }


});




jQuery(function () { //ждем загрузки страницы
    jQuery('.modal-body legend').on('click', function () { //ловим клик на legend

        //если внутри legend у нас "+", меняем его на минус, и наоборот
        //надо бы как-то поизящнее, но это работает :-)
        if (jQuery(this).text() == "+") {
            jQuery(this).text("−");
        }
        else {
            jQuery(this).text("+");
        }

        var currthread = jQuery(this).parent("fieldset"); //пишем в переменную currthread ближайший родительский fieldset
        //и удаляем/добавляем всем ее детям класс closed
        jQuery(currthread).children("fieldset").toggleClass("closed");
    });



});


