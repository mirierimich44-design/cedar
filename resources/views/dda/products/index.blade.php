@extends('layouts.app')
@section('title', 'DDA Products')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">DDA Products</h1>
</section>

<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Products Flagged as Dangerous Drugs'])
        <p class="text-muted" style="margin-bottom:12px">
            To flag a product as a DDA, go to <a href="{{ action([\App\Http\Controllers\ProductController::class, 'index']) }}">Products</a>,
            edit the product and enable the <strong>Dangerous Drug (DDA)</strong> checkbox.
        </p>

        @if($products->isEmpty())
            <div class="alert alert-info">No products are flagged as DDA yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dda_products_table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Linked DDA Drug</th>
                            <th>Class</th>
                            <th>Schedule</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><strong>{{ $product->name }}</strong></td>
                            <td>{{ $product->sku }}</td>
                            <td>{{ optional($product->ddaDrug)->name ?? '<span class="text-muted">Not linked</span>' }}</td>
                            <td>{{ optional($product->ddaDrug)->class ?? '—' }}</td>
                            <td>{{ optional($product->ddaDrug)->schedule ? 'Schedule '.optional($product->ddaDrug)->schedule : '—' }}</td>
                            <td>
                                <a href="{{ action([\App\Http\Controllers\ProductController::class, 'edit'], $product->id) }}" class="btn btn-xs btn-info">
                                    <i class="fa fa-edit"></i> Edit Product
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endcomponent
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('#dda_products_table').DataTable();
});
</script>
@endsection
