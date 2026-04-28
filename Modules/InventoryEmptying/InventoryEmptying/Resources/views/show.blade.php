@foreach ($groupbedContent as $branch=>$products)

    <div class="row">
            <div class="col-md-12"><strong>{{$branch}}</strong></div>
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table class="table table-condensed bg-gray">
                                <thead>
                                    <tr class="bg-green">
                                        <th>SKU</th>
                                        <th>@lang('inventoryemptying::app.product')</th>
                                        <th>@lang('inventoryemptying::app.before_empty')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                        @foreach ($products as $product)
                                            @php
                                            
                                                $productName = $product->variation->product->name;
                                                $varname='';

                                                $varname = ($product->variation->name == 'DUMMY'?'':' ( '. $product->variation->name .' )') ;
                                                $skuAndSubSku = ($product->variation->product->type =='single'?$product->variation->product->sku  : $product->variation->sub_sku) ;
                                            
                                            @endphp
                                            <tr>
                                                <td>{{  $skuAndSubSku }}</td>
                                                <td>{{  $productName .  $varname}}</td>
                                                <td>{{  intval($product->qty_before)  }}</td>
                                            </tr>

                                            @endforeach

                            
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                </div>
            </div>
    </div>
@endforeach
