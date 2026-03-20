<div class="modal fade no-print" id="recent_transactions_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">@lang('lang_v1.recent_transactions')</h4>
			</div>
			<div class="modal-body">
				{{-- Receipt Search --}}
				<div style="margin-bottom: 16px; padding: 12px; background: #f8fafc; border-radius: 8px; border: 1px solid #e2e8f0;">
					<div style="display: flex; gap: 8px;">
						<input type="text" id="receipt_search_input" class="form-control" placeholder="Search by receipt/invoice number..." style="flex: 1;">
						<button type="button" id="receipt_search_btn" class="btn btn-primary">
							<i class="fa fa-search"></i> Search
						</button>
					</div>
					<div id="receipt_search_results" style="margin-top: 12px; display: none;"></div>
				</div>

				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#tab_final" data-toggle="tab" aria-expanded="true"><b><i class="fa fa-check"></i> @lang('sale.final')</b></a></li>

						<li class=""><a href="#tab_quotation" data-toggle="tab" aria-expanded="false"><b><i class="fa fa-terminal"></i> @lang('lang_v1.quotation')</b></a></li>

						<li class=""><a href="#tab_draft" data-toggle="tab" aria-expanded="false"><b><i class="fa fa-terminal"></i> @lang('sale.draft')</b></a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="tab_final">
						</div>
						<div class="tab-pane" id="tab_quotation">
						</div>
						<div class="tab-pane" id="tab_draft">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
			    <button type="button" class="tw-dw-btn tw-dw-btn-neutral tw-text-white" data-dismiss="modal">@lang('messages.close')</button>
			</div>
		</div><!-- /.modal-content -->
	</div><!-- /.modal-dialog -->
</div>

<script>
$(document).ready(function() {
	$('#receipt_search_btn').click(function() {
		var query = $('#receipt_search_input').val().trim();
		if (!query) {
			toastr.warning('Enter a receipt/invoice number');
			return;
		}
		$('#receipt_search_results').html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Searching...</div>').show();
		$.get('/sells/pos/search-receipt', { query: query }, function(r) {
			if (r.html) {
				$('#receipt_search_results').html(r.html);
			} else {
				$('#receipt_search_results').html('<p class="text-muted text-center">No receipt found</p>');
			}
		}).fail(function() {
			$('#receipt_search_results').html('<p class="text-danger text-center">Search failed</p>');
		});
	});
	$('#receipt_search_input').keypress(function(e) {
		if (e.which == 13) $('#receipt_search_btn').click();
	});
});
</script>