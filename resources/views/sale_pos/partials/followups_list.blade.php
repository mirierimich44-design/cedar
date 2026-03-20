<!-- Follow-ups List for POS Modal -->
@if($followups->isEmpty())
    <div class="text-center text-muted" style="padding: 30px;">
        <i class="fa fa-inbox fa-3x"></i>
        <p style="margin-top: 10px;">No pending follow-ups</p>
    </div>
@else
    <div class="table-responsive">
        <table class="table table-bordered table-condensed table-striped">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Customer</th>
                    <th>Qty</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($followups as $followup)
                <tr>
                    <td>
                        <strong>{{ $followup->product ? $followup->product->name : '-' }}</strong>
                        @if($followup->variation && $followup->variation->name !== 'DUMMY')
                            <br><small class="text-muted">{{ $followup->variation->name }}</small>
                        @endif
                    </td>
                    <td>
                        {{ $followup->customer_name ?? '-' }}
                        <br><small class="text-muted"><i class="fa fa-phone"></i> {{ $followup->customer_phone }}</small>
                    </td>
                    <td>{{ $followup->quantity }}</td>
                    <td style="max-width:150px; font-size:12px; color:#374151;">
                        {{ $followup->comment ?? '—' }}
                    </td>
                    <td>
                        @php
                            $statusClass = [
                                'pending' => 'bg-yellow',
                                'contacted' => 'bg-blue',
                                'resolved' => 'bg-green'
                            ][$followup->status] ?? 'bg-gray';
                        @endphp
                        <span class="label {{ $statusClass }}">{{ ucfirst($followup->status) }}</span>
                    </td>
                    <td>{{ $followup->created_at->format('d/m H:i') }}</td>
                    <td>
                        <div class="btn-group btn-group-xs">
                            <button type="button" class="btn btn-info print-followup-btn"
                                    data-followup-id="{{ $followup->id }}"
                                    title="Print Follow-up">
                                <i class="fa fa-print"></i>
                            </button>
                            <button type="button" class="btn btn-primary update-followup-status-btn"
                                    data-followup-id="{{ $followup->id }}"
                                    data-status="{{ $followup->status }}"
                                    data-comment="{{ $followup->comment }}"
                                    title="Update Status / Comment">
                                <i class="fa fa-edit"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Update Status Modal for Followups in POS -->
    <div class="modal fade" id="pos_followup_status_modal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Update Follow-up</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="pos_update_followup_id">
                    <div class="form-group">
                        <label>Status:</label>
                        <select id="pos_new_followup_status" class="form-control">
                            <option value="pending">Pending</option>
                            <option value="contacted">Contacted</option>
                            <option value="resolved">Resolved</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comment:</label>
                        <textarea id="pos_followup_comment" class="form-control" rows="3"
                                  placeholder="Add or update comment..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="pos_save_followup_status">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function() {
            // Open status modal
            $(document).off('click', '.update-followup-status-btn').on('click', '.update-followup-status-btn', function() {
                var id = $(this).data('followup-id');
                var status = $(this).data('status');
                var comment = $(this).data('comment') || '';
                $('#pos_update_followup_id').val(id);
                $('#pos_new_followup_status').val(status);
                $('#pos_followup_comment').val(comment);
                $('#pos_followup_status_modal').modal('show');
            });

            // Save status + comment
            $('#pos_save_followup_status').off('click').on('click', function() {
                var id = $('#pos_update_followup_id').val();
                var status = $('#pos_new_followup_status').val();
                var comment = $('#pos_followup_comment').val();
                $.ajax({
                    url: '{{ action([\App\Http\Controllers\FollowupController::class, "updateStatus"], ["id" => "__ID__"]) }}'.replace('__ID__', id),
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        status: status,
                        comment: comment
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.msg);
                            $('#pos_followup_status_modal').modal('hide');
                            // Reload the followups list
                            $('a[href="#existing_followups_tab"]').trigger('shown.bs.tab');
                        } else {
                            toastr.error(response.msg);
                        }
                    }
                });
            });

            // Print followup (using receipt section like POS sales)
            $(document).off('click', '.print-followup-btn').on('click', '.print-followup-btn', function() {
                var followupId = $(this).data('followup-id');
                var $btn = $(this);
                $btn.prop('disabled', true).find('i').removeClass('fa-print').addClass('fa-spinner fa-spin');

                $.ajax({
                    url: '{{ url("pos-customer-followups") }}/' + followupId + '/receipt',
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
                        toastr.error('Failed to print follow-up');
                        $btn.prop('disabled', false).find('i').removeClass('fa-spinner fa-spin').addClass('fa-print');
                    }
                });
            });
        });
    </script>
@endif
