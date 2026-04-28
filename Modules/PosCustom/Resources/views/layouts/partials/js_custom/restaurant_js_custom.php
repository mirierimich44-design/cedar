<script>
//#JCN POS Display
//Include restaurant.js to work with POSDisplay

    $(document).ready(function() {
    //If location is set then show tables.
    getLocationTables($('input#location_id').val());

    $('select#select_location_id').change(function() {
        var location_id = $(this).val();
        getLocationTables(location_id);
    });

    $(document).on('click', 'button.add_modifier', function() {
        var checkbox = $(this)
            .closest('div.modal-content')
            .find('input:checked');
        selected = [];
        checkbox.each(function() {
            selected.push($(this).val());
        });
        var index = $(this)
            .closest('div.modal-content')
            .find('input.index')
            .val();

        var quantity = __read_number($(this).closest('tr').find('input.pos_quantity'));
        add_selected_modifiers(selected, index, quantity);
        
    });
    $(document).on('click', '#refresh_orders', function() {
        refresh_orders();
    });

    //Auto refresh orders
    if ($('#refresh_orders').length > 0) {
        var refresh_interval = parseInt($('#__orders_refresh_interval').val()) * 1000;

        setInterval(function(){ 
            refresh_orders();
        }, refresh_interval);
    }
});
if ($('#is_running_order').val() === '1') {
    console.log('Session indicates a new running order. Refreshing orders...');
    refresh_orders();
}
function getLocationTables(location_id) {
    var transaction_id = $('span#restaurant_module_span').data('transaction_id');

    if (location_id != '') {
        $.ajax({
            method: 'GET',
            url: '/modules/data/get-pos-details',
            data: { location_id: location_id, transaction_id: transaction_id },
            dataType: 'html',
            success: function(result) {
                $('span#restaurant_module_span').html(result);
                //REPAIR MODULE:set technician from repair module
                if ($("#repair_technician").length) {
                    $("select#res_waiter_id").val($("#repair_technician").val()).change();
                }
            },
        });
    }
}

function add_selected_modifiers(selected, index, quantity = 1) {
    if (selected.length > 0) {
        $.ajax({
            method: 'GET',
            url: $('button.add_modifier').data('url'),
            data: { selected: selected, index: index, quantity: quantity },
            dataType: 'html',
            success: function(result) {
                if (result != '') {
                    $('table#pos_table tbody')
                        .find('tr')
                        .each(function() {
                            if ($(this).data('row_index') == index) {
                                $(this)
                                    .find('td:first .selected_modifiers')
                                    .html(result);
                                //#JCN POS Display
                                var dataHTML = $(this).prop('outerHTML');
                                save_data_local_storage(dataHTML, 'update');
                                return false;
                            }
                        });

                    //Update total price.
                    pos_total_row();
                }
            },
        });
    } else {
        $('table#pos_table tbody')
            .find('tr')
            .each(function() {
                if ($(this).data('row_index') == index) {
                    $(this)
                        .find('td:first .selected_modifiers')
                        .html('');
                        //#JCN POS Display
                        var dataHTML = $(this).prop('outerHTML');
                        save_data_local_storage(dataHTML, 'update');
                    return false;
                }
            });

        //Update total price.
        pos_total_row();
    }
}

function refresh_orders() {
    $('.overlay').removeClass('hide');
    var orders_for = $('input#orders_for').val();
    var service_staff_id = '';
    if ($('select#service_staff_id').val()) {
        service_staff_id = $('select#service_staff_id').val();
    }
    $.ajax({
        method: 'POST',
        url: '/modules/refresh-orders-list',
        data: { orders_for: orders_for, service_staff_id: service_staff_id },
        dataType: 'html',
        success: function(data) {
            $('#orders_div').html(data);
            $('.overlay').addClass('hide');
        },
    });

    $.ajax({
        method: 'POST',
        url: '/modules/refresh-line-orders-list',
        data: { orders_for: orders_for, service_staff_id: service_staff_id },
        dataType: 'html',
        success: function(data) {
            $('#line_orders_div').html(data);
            $('.overlay').addClass('hide');
        },
    });
}
</script>
