<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Customer Purchase History</h4>
        </div>
        <div class="modal-body">
            @if($transactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Invoice No</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                                <tr>
                                    <td>{{ @format_date($transaction->transaction_date) }}</td>
                                    <td>{{ $transaction->invoice_no }}</td>
                                    <td>
                                        <small>
                                            @foreach($transaction->sell_lines as $line)
                                                {{ $line->product->name }} ({{ (float)$line->quantity }} {{ $line->product->unit->short_name }})<br>
                                            @endforeach
                                        </small>
                                    </td>
                                    <td>@format_currency($transaction->final_total)</td>
                                    <td>
                                        <button type="button" class="btn btn-xs btn-primary repeat-order" data-transaction_id="{{ $transaction->id }}">
                                            <i class="fa fa-refresh"></i> Repeat
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-center">No previous transactions found for this customer.</p>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>
