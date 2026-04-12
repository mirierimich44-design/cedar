<!-- Shop Orders List for POS Modal -->
@if($orders->isEmpty())
    <div class="text-center text-muted" style="padding: 30px;">
        <i class="fa fa-inbox fa-3x"></i>
        <p style="margin-top: 10px;">No pending shop orders</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-striped">
            <thead>
                <tr>
                    <th>Ref #</th>
                    <th>Items</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td><strong>{{ $order->ref_no }}</strong></td>
                    <td>
                        <span class="badge badge-info">{{ $order->orderLines->count() }}</span>
                        <button type="button" class="btn btn-xs btn-default view-order-items"
                                data-toggle="popover"
                                data-placement="left"
                                data-html="true"
                                data-content="@foreach($order->orderLines as $line){{ $line->product ? $line->product->name : ($line->custom_product_name ?? '-') }} x {{ number_format($line->quantity) }}<br>@endforeach">
                            <i class="fa fa-eye"></i>
                        </button>
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($order->notes, 30) }}</td>
                    <td>
                        @php
                            $statusClass = [
                                'pending' => 'bg-yellow',
                                'processing' => 'bg-blue',
                                'completed' => 'bg-green',
                                'cancelled' => 'bg-red'
                            ][$order->status] ?? 'bg-gray';
                        @endphp
                        <span class="label {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td>{{ $order->created_at->format('d/m H:i') }}</td>
                    <td>
                        <div class="btn-group btn-group-xs">
                            <button type="button" class="btn btn-info print-order-btn"
                                    data-order-id="{{ $order->id }}"
                                    title="Print Order">
                                <i class="fa fa-print"></i>
                            </button>
                            <button type="button" class="btn btn-success convert-order-to-sale"
                                    data-order-id="{{ $order->id }}"
                                    title="Convert to Sale">
                                <i class="fa fa-shopping-cart"></i>
                            </button>
                             <button type="button" class="btn btn-warning edit-order-pos-btn"
                                    data-order-id="{{ $order->id }}"
                                    data-notes="{{ $order->notes }}"
                                    data-contact-id="{{ $order->contact_id }}"
                                    data-lines="{{ json_encode($order->orderLines->map(function($line){
                                        return [
                                            'product_id' => $line->product_id,
                                            'variation_id' => $line->variation_id,
                                            'is_custom' => $line->product_id ? false : true,
                                            'name' => $line->product ? $line->product->name : ($line->custom_product_name ?? ''),
                                            'variation' => $line->variation ? $line->variation->name : '',
                                            'sub_sku' => $line->product ? $line->product->sub_sku : '',
                                            'quantity' => $line->quantity,
                                            'unit_price' => $line->unit_price
                                        ];
                                    })) }}"
                                    title="Edit Order">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-primary update-order-status-btn"
                                    data-order-id="{{ $order->id }}"
                                    data-status="{{ $order->status }}"
                                    title="Update Status">
                                <i class="fas fa-sync"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Update Status Modal for Orders in POS -->
    <div class="modal fade" id="pos_order_status_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Update Status</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pos_update_order_id">
                    <div class="form-group">
                        <label>Status:</label>
                        <select id="pos_new_order_status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="pos_save_order_status">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            $('[data-toggle="popover"]').popover();

            // Edit order in POS
            $(document).off('click', '.edit-order-pos-btn').on('click', '.edit-order-pos-btn', function() {
                var orderId = $(this).data('order-id');
                var notes = $(this).data('notes');
                var contactId = $(this).data('contact-id');
                var lines = $(this).data('lines');

                // Trigger custom event to be caught by orders_modal.blade.php
                $(document).trigger('pos_edit_order', {
                    id: orderId,
                    notes: notes,
                    contact_id: contactId,
                    lines: lines
                });
            });

            // Open status modal
            $(document).off('click', '.update-order-status-btn').on('click', '.update-order-status-btn', function() {
                var id = $(this).data('order-id');
                var status = $(this).data('status');
                $('#pos_update_order_id').val(id);
                $('#pos_new_order_status').val(status);
                $('#pos_order_status_modal').modal('show');
            });

            // Save status
            $('#pos_save_order_status').off('click').on('click', function() {
                var id = $('#pos_update_order_id').val();
                var status = $('#pos_new_order_status').val();
                $.ajax({
                    url: '{{ action([\App\Http\Controllers\OrderController::class, "updateStatus"], ["id" => "__ID__"]) }}'.replace('__ID__', id),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            $('#pos_order_status_modal').modal('hide');
                            // Reload the orders list
                            $('a[href="#existing_orders_tab"]').trigger('shown.bs.tab');
                        } else {
                            toastr.error(response.msg);
                        }
                    }
                });
            });

            // Print order (using receipt section like POS sales)
            $(document).off('click', '.print-order-btn').on('click', '.print-order-btn', function() {
                var orderId = $(this).data('order-id');
                var $btn = $(this);
                $btn.prop('disabled', true).find('i').removeClass('fa-print').addClass('fa-spinner fa-spin');

                $.ajax({
                    url: '{{ url("pos-customer-orders") }}/' + orderId + '/receipt',
                    method: 'GET',
                    dataType: 'json',
                    success: function(result) {
                        if (result.success) {
                            $('#receipt_section').html(result.html_content);

                            var title = document.title;
                            if (result.print_title) {
                                document.title = result.print_title;
                            }

                            // Use same print function as POS sales
                            if (typeof __print_receipt === 'function') {
                                __print_receipt('receipt_section');
                            } else {
                                window.print();
                            }

                            setTimeout(function() {
                                document.title = title;
                            }, 1000);
                        } else {
                            toastr.error(result.msg || 'Failed to print');
                        }

                        $btn.prop('disabled', false).find('i').removeClass('fa-spinner fa-spin').addClass('fa-print');
                    },
                    error: function() {
                        toastr.error('Failed to print order');
                        $btn.prop('disabled', false).find('i').removeClass('fa-spinner fa-spin').addClass('fa-print');
                    }
                });
            });
        });
    </script>
@endif
