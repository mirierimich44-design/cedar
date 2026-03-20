$(document).ready(function () {
    if ($('#dashboard_date_filter').length == 1) {
        dateRangeSettings.startDate = moment();
        dateRangeSettings.endDate = moment();
        $('#dashboard_date_filter').daterangepicker(dateRangeSettings, function (start, end) {
            $('#dashboard_date_filter span').html(
                start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
            );
            update_statistics(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            if ($('#quotation_table').length && $('#dashboard_location').length) {
                quotation_datatable.ajax.reload();
            }
        });

        update_statistics(moment().format('YYYY-MM-DD'), moment().format('YYYY-MM-DD'));
    }

    $('#dashboard_location').change(function (e) {
        var start = $('#dashboard_date_filter')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');

        var end = $('#dashboard_date_filter')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');

        update_statistics(start, end);
    });

    //atock alert datatables
    var stock_alert_table = $('#stock_alert_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY: "75vh",
        scrollX: true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            "url": '/home/product-stock-alert',
            "data": function (d) {
                if ($('#stock_alert_location').length > 0) {
                    d.location_id = $('#stock_alert_location').val();
                }
            }
        },
        fnDrawCallback: function (oSettings) {
            __currency_convert_recursively($('#stock_alert_table'));
        },
    });

    $('#stock_alert_location').change(function () {
        stock_alert_table.ajax.reload();
    });
    //payment dues datatables
    purchase_payment_dues_table = $('#purchase_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY: "75vh",
        scrollX: true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            "url": '/home/purchase-payment-dues',
            "data": function (d) {
                if ($('#purchase_payment_dues_location').length > 0) {
                    d.location_id = $('#purchase_payment_dues_location').val();
                }
            }
        },
        fnDrawCallback: function (oSettings) {
            __currency_convert_recursively($('#purchase_payment_dues_table'));
        },
    });

    $('#purchase_payment_dues_location').change(function () {
        purchase_payment_dues_table.ajax.reload();
    });

    //Sales dues datatables
    sales_payment_dues_table = $('#sales_payment_dues_table').DataTable({
        processing: true,
        serverSide: true,
        ordering: false,
        searching: false,
        scrollY: "75vh",
        scrollX: true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            "url": '/home/sales-payment-dues',
            "data": function (d) {
                if ($('#sales_payment_dues_location').length > 0) {
                    d.location_id = $('#sales_payment_dues_location').val();
                }
            }
        },
        fnDrawCallback: function (oSettings) {
            __currency_convert_recursively($('#sales_payment_dues_table'));
        },
    });

    $('#sales_payment_dues_location').change(function () {
        sales_payment_dues_table.ajax.reload();
    });

    //Stock expiry report table
    stock_expiry_alert_table = $('#stock_expiry_alert_table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        scrollY: "75vh",
        scrollX: true,
        scrollCollapse: true,
        fixedHeader: false,
        dom: 'Btirp',
        ajax: {
            url: '/reports/stock-expiry',
            data: function (d) {
                d.exp_date_filter = $('#stock_expiry_alert_days').val();
            },
        },
        order: [[3, 'asc']],
        columns: [
            { data: 'product', name: 'p.name' },
            { data: 'location', name: 'l.name' },
            { data: 'stock_left', name: 'stock_left' },
            { data: 'exp_date', name: 'exp_date' },
        ],
        fnDrawCallback: function (oSettings) {
            __show_date_diff_for_human($('#stock_expiry_alert_table'));
            __currency_convert_recursively($('#stock_expiry_alert_table'));
        },
    });

    if ($('#quotation_table').length) {
        quotation_datatable = $('#quotation_table').DataTable({
            processing: true,
            serverSide: true,
            fixedHeader: false,
            aaSorting: [[0, 'desc']],
            "ajax": {
                "url": '/sells/draft-dt?is_quotation=1',
                "data": function (d) {
                    if ($('#dashboard_location').length > 0) {
                        d.location_id = $('#dashboard_location').val();
                    }
                }
            },
            columnDefs: [{
                "targets": 4,
                "orderable": false,
                "searchable": false
            }],
            columns: [
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'name', name: 'contacts.name' },
                { data: 'business_location', name: 'bl.name' },
                { data: 'action', name: 'action' }
            ]
        });
    }

    // Drill-down interactions
    // Purchase Due -> Scroll to table
    $('.purchase_due').closest('.tw-rounded-xl').css('cursor', 'pointer').click(function () {
        if ($('#purchase_payment_dues_table').length) {
            $('html, body').animate({
                scrollTop: $("#purchase_payment_dues_table").offset().top - 200
            }, 500);
        }
    });

    // Invoice Due (Sales Payment Dues)
    $('.invoice_due').closest('.tw-rounded-xl').css('cursor', 'pointer').click(function () {
        if ($('#sales_payment_dues_table').length) {
            $('html, body').animate({
                scrollTop: $("#sales_payment_dues_table").offset().top - 200
            }, 500);
        }
    });

    // Stock Alert
    // There isn't a direct single widget for stock alert count usually, but if there is...
    // Adjusting based on standard UI. The "Product Stock Alert" is a table itself. 
});


function update_statistics(start, end) {
    var location_id = '';
    if ($('#dashboard_location').length > 0) {
        location_id = $('#dashboard_location').val();
    }
    var data = { start: start, end: end, location_id: location_id };
    //get purchase details
    var loader = '<i class="fas fa-sync fa-spin fa-fw margin-bottom"></i>';
    // Don't replace with loader if we want to animate from 0 or previous value, but for now standard behavior is fine
    // or we can allow the animation to start from 0 each time

    $.ajax({
        method: 'get',
        url: '/home/get-totals',
        dataType: 'json',
        data: data,
        success: function (data) {
            //purchase details
            animateValue($('.total_purchase'), data.total_purchase);
            animateValue($('.purchase_due'), data.purchase_due);

            //sell details
            animateValue($('.total_sell'), data.total_sell);
            animateValue($('.invoice_due'), data.invoice_due);

            //expense details
            animateValue($('.total_expense'), data.total_expense);

            var total_purchase_return = data.total_purchase_return - data.total_purchase_return_paid;
            animateValue($('.total_purchase_return'), total_purchase_return);

            var total_sell_return_due = data.total_sell_return - data.total_sell_return_paid;
            animateValue($('.total_sell_return'), total_sell_return_due);

            $('.total_sr').html(__currency_trans_from_en(data.total_sell_return, true));
            $('.total_srp').html(__currency_trans_from_en(data.total_sell_return_paid, true));
            $('.total_pr').html(__currency_trans_from_en(data.total_purchase_return, true));
            $('.total_prp').html(__currency_trans_from_en(data.total_purchase_return_paid, true));

            animateValue($('.net'), data.net);

            // assign tooltip total_sell_return 
            var lang = $('#total_srp').data('value');
            if (lang) {
                var splitlang = lang.split('-');

                var newContent = "<p class='mb-0 text-muted fs-10 mt-5'>" + splitlang[0] + ": <span class=''>" + __currency_trans_from_en(data.total_sell_return, true) + "</span><br>" + splitlang[1] + ": <span class=''>" + __currency_trans_from_en(data.total_sell_return_paid, true) + "</span></p>";
                $('#total_srp').attr('data-content', newContent)
            }

            // assign tooltip total_purchase_return 
            var lang = $('#total_prp').data('value');
            if (lang) {
                var splitlang = lang.split('-');

                var newContent = "<p class='mb-0 text-muted fs-10 mt-5'>" + splitlang[0] + ": <span class=''>" + __currency_trans_from_en(data.total_purchase_return, true) + "</span><br>" + splitlang[1] + ": <span class=''>" + __currency_trans_from_en(data.total_purchase_return_paid, true) + "</span></p>";

                $('#total_prp').attr('data-content', newContent);
            }

        },
    });
}

function animateValue($element, value) {
    // Check if value is valid number
    if (isNaN(value) || value === null) {
        $element.html(__currency_trans_from_en(value, true));
        return;
    }

    // We assume starting from 0 for the effect, or we could parse current text
    var start = 0;
    var end = parseFloat(value);
    var duration = 1000;
    var range = end - start;
    var current = start;
    var increment = end > start ? 1 : -1;
    var stepTime = Math.abs(Math.floor(duration / (range * 10))); // adjustments for speed
    if (stepTime < 10) stepTime = 10; // minimum step time

    // Simple Interval Animation
    // For smoother animation with currency formatting, we can use a library like CountUp.js 
    // but here is a custom simple implementation that updates at intervals

    $({ countNum: start }).animate({ countNum: end }, {
        duration: 1000,
        easing: 'linear',
        step: function () {
            // Update visual with formatted currency
            $element.html(__currency_trans_from_en(this.countNum, true));
        },
        complete: function () {
            $element.html(__currency_trans_from_en(this.countNum, true));
        }
    });
}
