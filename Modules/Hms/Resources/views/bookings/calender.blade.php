@extends('layouts.app')
@section('title', __('lang_v1.calendar'))

@section('content')
    @include('hms::layouts.nav')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black" >@lang('lang_v1.calendar')</h1>
        <p><i class="fa fa-info-circle"></i> @lang('hms::lang.calender_help_text') </p>
    </section>

    <!-- Main content -->
    <section class="content">
        @component('components.widget')
            <div class="box-header">
                <div class="box-tools pull-left">
                    <h3>@lang('hms::lang.jump_to')</h3>
                    <input type="text" value="{{ request()->input('date') ? request()->input('date') : '' }}"
                        class="form-control date_picker">
                </div>
                <div class="box-tools pull-left" style="margin-left: 25px; margin-top:45px;">
                    <a class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="week_prev" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                    <a class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm  mt-5" id="day_prev" aria-label="Previous">
                        <span aria-hidden="true">&lsaquo;</span>
                    </a>
                </div>

                <div class="box-tools pull-right" style="margin-top:45px;">
                    <a class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="day_next" aria-label="next">
                        <span aria-hidden="true">&rsaquo;</span>
                    </a>
                    <a class="tw-dw-btn tw-dw-btn-primary tw-text-white tw-dw-btn-sm" id="week_next" aria-label="next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </div>
            </div>
            <table class="table table-bordered " id="bookings_calender">
                <thead>
                    <tr>
                        <th style="width: 150px;">
                            {!! Form::select('type_id', $types, request()->input('type_id') ? request()->input('type_id') : null, [
                                'class' => 'form-control',
                                'id' => 'type_id',
                                'placeholder' => __('hms::lang.type'),
                            ]) !!}
                        </th>
                        {!! $date_html !!}
                    </tr>
                    {!! $html !!}
                </thead>
            </table>
        @endcomponent

    </section>

    {{-- Room Occupancy Grid --}}
    <section class="content" style="margin-top:-10px;">
        <div class="box box-primary">
            <div class="box-header with-border" style="display:flex;align-items:center;justify-content:space-between;">
                <h3 class="box-title"><i class="fa fa-table"></i> Room Occupancy — Next 14 Days</h3>
                <button id="refresh-occupancy-grid" class="btn btn-sm btn-default no-print"><i class="fa fa-refresh"></i> Refresh</button>
            </div>
            <div class="box-body" style="overflow-x:auto;">
                <div id="occupancy-grid-loading" style="text-align:center;padding:30px;color:#94a3b8;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i><p style="margin-top:8px;">Loading grid…</p>
                </div>
                <div id="occupancy-grid" style="display:none;"></div>
                <div style="margin-top:12px;display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:#475569;" class="no-print">
                    <span><span style="display:inline-block;width:14px;height:14px;background:#bbf7d0;border-radius:3px;vertical-align:middle;margin-right:4px;"></span>Available</span>
                    <span><span style="display:inline-block;width:14px;height:14px;background:#fca5a5;border-radius:3px;vertical-align:middle;margin-right:4px;"></span>Occupied</span>
                    <span><span style="display:inline-block;width:14px;height:14px;background:#bfdbfe;border-radius:3px;vertical-align:middle;margin-right:4px;"></span>Check-in</span>
                    <span><span style="display:inline-block;width:14px;height:14px;background:#fde68a;border-radius:3px;vertical-align:middle;margin-right:4px;"></span>Check-out</span>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->

@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            var currentDate = new Date();
            var currentDateTime = moment(currentDate);

            $('.date_picker').datetimepicker({
                format: moment_date_format,
                ignoreReadonly: true,
                defaultDate: currentDateTime
            });

            $('.date_picker').on('dp.change', function(e) {

                window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                    "&date=" + $('.date_picker').val();

            });

            $('#type_id').on('change', function() {
                window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                    "&date=" + $('.date_picker').val();
            })


            $('#week_next').on('click', function() {
                var weekNext = "{{ request()->input('week_next') }}";
                if (weekNext == '') {
                    weekNext = 1;
                } else {
                    weekNext++;
                }
                window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                    "&week_next=" + weekNext;
            })

            $('#week_prev').on('click', function() {

                var weekNext = "{{ request()->input('week_next') }}";
                if (weekNext == '') {
                    weekNext = -1;
                } else {
                    weekNext--;
                }
                window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                    "&week_next=" + weekNext;
            })


            $('#day_next').on('click', function() {

                var daynext = "{{ request()->input('day_next') }}";
                if (daynext == '') {
                    daynext = 1;
                } else {
                    daynext++;
                }
                window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                    "&day_next=" + daynext;
            })

            $('#day_prev').on('click', function() {

                var daynext = "{{ request()->input('day_next') }}";
                if (daynext == '') {
                    daynext = -1;
                } else {
                    daynext--;
                }
                window.location.href = "{{ route('booking_calendar') }}?type_id=" + $('#type_id').val() +
                    "&day_next=" + daynext;
            })

            $(".add_booking").hover(
                function() {
                    $(this).find(".add_booking_div").fadeIn();
                },
                function() {
                    $(this).find(".add_booking_div").fadeOut();
                }
            );


        });

        // ── Room Occupancy Grid ───────────────────────────────────────────
        function loadOccupancyGrid() {
            $('#occupancy-grid-loading').show();
            $('#occupancy-grid').hide();
            $.get('{{ route("hms.room_occupancy_grid") }}', function(data) {
                if (!data.success) {
                    $('#occupancy-grid-loading').html('<p class="text-muted text-center">Could not load grid.</p>');
                    return;
                }
                var bgMap = { available: '#bbf7d0', occupied: '#fca5a5', checkin: '#bfdbfe', checkout: '#fde68a' };
                var iconMap = { available: '✓', occupied: '●', checkin: '→', checkout: '←' };

                var tbl = '<table style="border-collapse:collapse;font-size:12px;min-width:100%;">';
                tbl += '<thead><tr><th style="padding:6px 10px;background:#f8fafc;border:1px solid #e2e8f0;text-align:left;min-width:110px;white-space:nowrap;position:sticky;left:0;z-index:2;">Room</th>';
                data.dates.forEach(function(d) {
                    var isToday = d.date === data.today;
                    tbl += '<th style="padding:4px 6px;background:' + (isToday ? '#dbeafe' : '#f8fafc') + ';border:1px solid #e2e8f0;text-align:center;min-width:44px;font-weight:' + (isToday ? '800' : '500') + ';color:' + (isToday ? '#1d4ed8' : '#64748b') + ';">' + d.label + '</th>';
                });
                tbl += '</tr></thead><tbody>';
                data.rooms.forEach(function(room) {
                    tbl += '<tr><td style="padding:5px 10px;border:1px solid #e2e8f0;font-weight:600;background:#f8fafc;white-space:nowrap;position:sticky;left:0;z-index:1;">' + room.number + ' <small style="color:#94a3b8;font-weight:400;">— ' + room.type + '</small></td>';
                    data.dates.forEach(function(d) {
                        var st = room.occupancy[d.date] || 'available';
                        var bg = bgMap[st] || '#f1f5f9';
                        var ic = iconMap[st] || '';
                        tbl += '<td style="padding:3px;border:1px solid #e2e8f0;background:' + bg + ';text-align:center;" title="' + room.number + ' · ' + d.date + ' · ' + st + '">' + ic + '</td>';
                    });
                    tbl += '</tr>';
                });
                tbl += '</tbody></table>';
                $('#occupancy-grid').html(tbl).show();
                $('#occupancy-grid-loading').hide();
            }).fail(function() {
                $('#occupancy-grid-loading').html('<p class="text-muted text-center">Failed to load occupancy data.</p>');
            });
        }

        loadOccupancyGrid();
        $('#refresh-occupancy-grid').on('click', function() { loadOccupancyGrid(); });
        // ─────────────────────────────────────────────────────────────────
    </script>
@endsection

<style>
    .hotel-reservation-outer:last-child {
        padding-bottom: 5px;
        height: 30px;
    }

    .hotel-reservation-outer {
        height: 25px;
        width: 100%;
        position: relative;
    }

    .hotel-reservation-inner {
        height: 20px;
        width: 100%;
        border-radius: 2px;
        padding: 0 5px;
        color: #fff;
    }

    .bg-confirmed {
        background-color: #5ac5b6;
        border-color: #5ac5b6;
        color: #fff;
    }

    .add_booking_div {
        display: none;
        padding-top: 15px;
    }
</style>
