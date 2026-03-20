@extends('layouts.app')
@section('title', 'Purchase Price Variance Report')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">Purchase Price Variance (PPV) Report</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" 
                    id="ppv_report_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('product.sku')</th>
                                <th>@lang('sale.product')</th>
                                <th>Supplier 1 (Latest)</th>
                                <th>Price 1</th>
                                <th>Supplier 2</th>
                                <th>Price 2</th>
                                <th>Supplier 3</th>
                                <th>Price 3</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready( function(){
    if ($('#ppv_report_table').length) {
        ppv_report_table = $('#ppv_report_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/reports/purchase-price-variance',
            columns: [
                { data: 'sub_sku', name: 'variations.sub_sku' },
                { data: 'product_name', name: 'p.name' },
                { data: 'supplier_1', name: 'supplier_1', searchable: false, orderable: false },
                { data: 'price_1', name: 'price_1', searchable: false, orderable: false },
                { data: 'supplier_2', name: 'supplier_2', searchable: false, orderable: false },
                { data: 'price_2', name: 'price_2', searchable: false, orderable: false },
                { data: 'supplier_3', name: 'supplier_3', searchable: false, orderable: false },
                { data: 'price_3', name: 'price_3', searchable: false, orderable: false },
            ],
        });
    }
});
</script>
@endsection
