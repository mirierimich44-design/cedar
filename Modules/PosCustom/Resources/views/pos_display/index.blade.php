@inject('request', 'Illuminate\Http\Request')
@extends('PosCustom::layouts.app_ss')

@section('title', __('poscustom::lang.pos_display'))
{{--var_dump($pos_settings)--}}

{{--
{{var_dump($product->product_description)}}
{{$user_location->location_id}}
------------------
{{$business_id}}

--}}


{{-- INI #JCN Random image background login and customization  --}}
<style>

.heading-empty {
   flex-direction: column;  
   align-items: center;
   text-align: center;
   font-size: 24px;
   font-weight: bold;
   margin-bottom: 20px;
   }

.table-styled {
      border-radius: 2rem;
      border: 3px solid white;

      /*Other functions*/
      /* background-color: aquamarine; */
      height: calc(100vh - 180px); /*left size*/
      overflow-x: auto;
      overflow-y: auto;
      text-align: center;
   }

.table-styled table {
      border-top: hidden;
      border-left: hidden;
      border-bottom: hidden;
      border-right: hidden;
      border-collapse: collapse;
   }

.table-styled table thead {
      position: sticky;
      top: 0;
      border-bottom: 2px solid #ccc;
   }
.table-styled table tfoot {
      position: sticky; 
      bottom: 0;
      border-bottom: 2px solid #ccc;
   }

.table-styled table thead tr,
.table-styled table tfoot tr {
      background-color: lightgray; 
      font-weight: bold;
      color: rgb(0, 0, 0);
      /* border-top: 2px solid #dee2e6; */
      text-align: center;
   }

.table-styled table th {
      text-align: center;
   }

.table-styled table th,
.table-styled table td {
      border: 10px solid white;
      padding: 1rem 2rem;
   }

.custom-footer {
   background-color: #fff;
   padding: 10px 0;
   box-shadow: 0 -8px 5px rgba(0, 0, 0, 0.1);
   display: flex;
   justify-content: space-around;
   align-items: center;
   flex-wrap: wrap;
   }
   
.footer-item-total {
   text-align: center;
   padding: 5px 10px;
   font-weight: bold;
   /* border: 1px solid #dee2e6; */
   }
.footer-item-total:first-child {
   border-left: none;
   }
</style>


{{-- INI #JCN animation banner dynamic autoplay  --}}
@php

	$is_mobile = isMobile(); 

   $message = $pos_settings['display_screen_heading'];

	$theme_divider_class = 'theme_' . $business_details->theme_color . '_divider';
   /* New style usin css/tailwind/app.css */
   $theme_pos_class = 'tw-bg-gradient-to-r tw-from-' . $business_details->theme_color . '-800'
   .' tw-to-'.$business_details->theme_color.'-500';
   
@endphp
{{-- END --}}
@section('content')

        {{--#JCN Include to have a responsive layout --}}
        {{-- Code base for responsive layout POS
                md:tw-w-3/4 = 75%
                md:tw-w-4/6 = 70% --}}
            <maindiv class="tw-flex tw-flex-col tw-flex-row  tw-bg-gray-100 tw-min-h-screen tw-w-full tw-gap-y-1 tw-px-1  ">
                <header class="tw-flex tw-gap-4 {{$theme_pos_class}} {{-- tw-p-1 --}} !tw-bg-white  tw-rounded-2xl " >

                  {{--#JCN Add the logo --}} 
                  <div  class="col-md-2 " style="align-content: initial" >
                     <a href="/">
                        @if(file_exists(public_path('img/logo.png')))
                           <img src="/img/logo.png" class="img-rounded" alt="Logo" width="160" > 
                        @elseif (file_exists(public_path('img/logo-small.png')))
                            <img src="/img/logo-small.png" class="img-rounded" alt="Logo" width="50" > 
                        @else
                            <p> {{ config('app.name', 'ultimatePOS') }} </p>
                        @endif 
                     </a>
                  </div>
                  
                  {{--#JCN this the messages cfg in v6.7--}}
                  <div class="col-md-9 tw-self-center {{-- tw-bg-green-600 --}} tw-rounded-2xl ">
                     {{--#JCN Using {!! $varaible !!} to get the html format --}}
                     {!! $pos_settings['display_screen_heading'] !!}
                  </div>

                  <div class="col-md-1 tw-self-center " >
                     <button type="button" title="{{ __('lang_v1.full_screen') }}" class="add-user-modal-btn btn-modal pull-right" id="full_screen" data-toggle="tooltip" data-placement="bottom">

                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="white" class="bi bi-arrows-fullscreen" viewBox="0 0 16 16">
                           <path fill-rule="evenodd" d="M5.828 10.172a.5.5 0 0 0-.707 0l-4.096 4.096V11.5a.5.5 0 0 0-1 0v3.975a.5.5 0 0 0 .5.5H4.5a.5.5 0 0 0 0-1H1.732l4.096-4.096a.5.5 0 0 0 0-.707m4.344 0a.5.5 0 0 1 .707 0l4.096 4.096V11.5a.5.5 0 1 1 1 0v3.975a.5.5 0 0 1-.5.5H11.5a.5.5 0 0 1 0-1h2.768l-4.096-4.096a.5.5 0 0 1 0-.707m0-4.344a.5.5 0 0 0 .707 0l4.096-4.096V4.5a.5.5 0 1 0 1 0V.525a.5.5 0 0 0-.5-.5H11.5a.5.5 0 0 0 0 1h2.768l-4.096 4.096a.5.5 0 0 0 0 .707m-4.344 0a.5.5 0 0 1-.707 0L1.025 1.732V4.5a.5.5 0 0 1-1 0V.525a.5.5 0 0 1 .5-.5H4.5a.5.5 0 0 1 0 1H1.732l4.096 4.096a.5.5 0 0 1 0 .707"/>
                         </svg>                     
                         
                     </button>
                  </div>
                  
               </header>
                <div class="tw-flex tw-flex-col md:tw-flex-row tw-flex-grow tw-gap-2  {{-- tw-gap-x-1 --}}">
                    <totals class="tw-flex tw-flex-col tw-flex-row  tw-w-full tw-bg-white  tw-order-2 md:tw-order-1 tw-rounded-2xl"> 
                        <Contenttotal class="tw-h-full  tw-rounded-2xl " >
                            <div class="tw-ml-2 customer_details {{-- tw-bg-gray-200 --}}">

                            </div>
                            <div class="table-responsive  table-styled pos_sell {{-- !tw-mt-2  --}}"  {{-- id="pos-display" --}}>
                                 <table class="table table-bordered table-striped ajax_view max-table" id="pos_table"  {{-- style="width: 100%" --}}>
                                    <thead >
                                        <tr>
                                            <th>#</th>
                                            <th
                                                class="tex-center tw-text-sm md:!tw-text-base tw-font-bold @if (!empty($pos_settings['inline_service_staff'])) col-md-3 @else col-md-4 @endif">
                                                @lang('sale.product')
                                                {{-- @show_tooltip(__('lang_v1.tooltip_sell_product_column')) --}}
                                            </th>
                                            <th
                                                    class="text-center tw-text-sm md:!tw-text-base tw-font-bold col-md-2">
                                                    @lang('sale.price_inc_tax')
                                            </th>
                                            <th
                                                class="text-center tw-text-sm md:!tw-text-base tw-font-bold col-md-2">
                                                @lang('sale.discount')
                                            </th>
                                            <th
                                                class="text-center tw-text-sm md:!tw-text-base tw-font-bold col-md-1">
                                                @lang('sale.qty')
                                            </th>

                                            @if (!empty($pos_settings['inline_service_staff']))
                                                <th
                                                    class="text-center tw-text-sm md:!tw-text-base tw-font-bold col-md-2">
                                                    @lang('restaurant.service_staff')
                                                </th>
                                            @endif
                                            <th 
                                                class="text-center tw-text-sm md:!tw-text-base tw-font-bold col-md-2">
                                                @lang('sale.subtotal')
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                       <!-- Item rows will be dynamically added here -->
                                    </tbody>
                                 </table>
                           </div>
                        </Contenttotal>  
                        {{--#JCN Start Footer--}}

                        <div  class="row col-md-12 tw-flex tw-self-center tw-items-center tw-justify-center pos_sell">
                            <div class="col-md-3">
                                <b class="tw-text-base md:tw-text-md tw-font-bold">(@lang('sale.item'):
                                    <span class="total_quantity tw-text-base md:tw-text-md tw-font-semibold">0</span>
                                )</b> 
                            </div>
                            <div class="col-md-4">
                                <b class="tw-text-base md:tw-text-lg tw-font-bold">@lang('sale.total'):</b>&nbsp;
                                <span class="price_total tw-text-base md:tw-text-lg tw-font-semibold display_currency"
                                data-currency_symbol="true">0</span>
                            </div>
                            <div class="col-md-5">
                                <b class="tw-text-base tw-text-green-900 tw-text-red-500 tw-font-bold md:tw-text-2xl">@lang('sale.total_payable'):</b>
                                <span class="tw-text-base tw-text-green-900 tw-text-red-500 md:tw-text-2xl tw-font-semibold display_currency"
                                    data-currency_symbol="true" id="total_payable">0</span>                               
                            </div>
                        </div>
                        <Footertotal  class="tw-items-center tw-justify-center {{-- tw-bg-gray-200 --}} tw-rounded-2xl ">
                            <div class="custom-footer pos_sell tw-rounded-2xl ">
                                <div class="footer-item-total ">
                                    <b class="tw-text-base md:tw-text-md tw-font-bold ">@lang('sale.shipping')(+):</b>
                                    <span class="tw-text-base md:tw-text-md tw-font-semibold display_currency"
                                        data-currency_symbol="true" id="shipping_charges_amount">9</span>
                                </div>
                                <div class="footer-item-total">
                                    <b class="tw-text-base md:tw-text-md tw-font-bold">@lang('sale.order_tax')(+):</b>
                                    <span class="tw-text-base md:tw-text-md tw-font-semibold display_currency"
                                        data-currency_symbol="true" id="order_tax">0</span>
                                </div>
                                @if (in_array('types_of_service', $enabled_modules))
                                <div class="footer-item-total">
                                   <strong>@lang('lang_v1.packing_charge'):</strong>+<span id="packing_charge"></span>
                                </div>
                                @endif
                                <div class="footer-item-total">
                                    <b class="tw-text-base md:tw-text-md tw-font-bold">@lang('sale.discount')(-):</b>
                                    <span class="tw-text-base md:tw-text-md tw-text-red-500 tw-font-semibold display_currency"
                                        data-currency_symbol="true" id="total_discount">0</span>                                
                                </div>
                                <div class="footer-item-total">
                                   <b class="tw-text-base md:tw-text-md tw-font-bold">@lang('lang_v1.redeemed')(-):</b>
                                   <span class="tw-text-base md:tw-text-md tw-text-red-500 tw-font-semibold display_currency"
                                       data-currency_symbol="true" id="rp_redeemed_modal">0</span>   
                                </div>
                            </div>
                       <Footertotal> 
                        
                    </totals>
                    <products class="tw-flex tw-flex-col tw-flex-row md:tw-w-3/4  tw-p-1 {{-- tw-bg-orange-800 --}} tw-order-1 md:tw-order-2 tw-rounded-2xl">
                     <Contentproducts class="tw-h-full  {{--  tw-bg-orange-800--}} tw-bg-white tw-rounded-2xl" >
                        {{-- <div class="slider tw-h-full"> --}}
                        <div style="height: calc(100vh - 63px);" class="md:tw-no-padding  tw-shadow-xl tw-border tw-border-gray-400/30 tw-rounded-lg">
                           <div id="myCarousel" class="carousel slide !tw-h-full tw-transition-all tw-duration-500 tw-ease-in-out" data-ride="carousel">
                              <!-- Indicators -->
                              <ol class="carousel-indicators">
                                  @foreach (range(1, 10) as $i)
                                      @if (isset($pos_settings['carousel_image_' . $i]))
                                          <li data-target="#myCarousel" data-slide-to="{{ $i - 1 }}" 
                                              class="tw-inline-block tw-mx-1 tw-border-2 !tw-border-black tw-rounded-full tw-w-4 tw-h-4 !tw-bg-white tw-opacity-90 tw-shadow-lg tw-cursor-pointer tw-transition-all tw-duration-300 hover:tw-bg-white hover:tw-opacity-100 {{ $i == 1 ? 'tw-bg-white tw-opacity-100' : 'tw-bg-gray-500' }}">
                                          </li>
                                      @endif
                                  @endforeach
                              </ol>
                              <!-- Wrapper for slides -->
                              <div class="carousel-inner {{-- !tw-h-[80vh] --}} tw-rounded-lg" >
                                  @foreach (range(1, 10) as $i)
                                      @if (isset($pos_settings['carousel_image_' . $i]))
                                          <div class="item {{ $i == 1 ? 'active' : '' }} !tw-h-full tw-relative">
                                              <img src="{{ url('uploads/carousel_images/' . $pos_settings['carousel_image_' . $i]) }}"
                                                  class="!tw-h-full !tw-w-full !tw-d-block !tw-mx-auto {{-- !tw-object-contain --}} tw-rounded-lg tw-transition-all tw-duration-500">
                                          </div>
                                      @endif
                                  @endforeach
                              </div>
                          </div>
                        </div>
                     </Contentproducts> 
                 </products> 
                </div>

            </maindiv>
@endsection

@section('javascript')
    <script>
        $(document).ready(function() {
            let storageUpdateTimer = null; // Declare the timer globally

            // Simple in-memory caches
            const productsCache = new Map(); // `${variationId}__${locationId}` -> product|null

            function makeProductKey(variationId, locationId) {
                return `${variationId}__${locationId}`;
            }

            async function getCustomerById(id) {
                try {
                    const response = await $.ajax({
                        url: "/contacts/customers",
                        method: "GET",
                        dataType: "json",
                        delay: 250
                    });
                    const sid = String(id);
                    const filteredCustomers = (response || []).filter(function(customer) { return String(customer.id) === sid; });
                    return filteredCustomers.length ? filteredCustomers[0] : null;
                } catch (e) {
                    return null;
                }
            }

            function setProductInCache(variationId, locationId, product) {
                productsCache.set(makeProductKey(variationId, locationId), product);
            }

            async function fetchProductsBulkOnce(variationIds, locationId) {
                const missing = [];
                for (var i = 0; i < variationIds.length; i++) {
                    const vid = variationIds[i];
                    const key = makeProductKey(vid, locationId);
                    if (!productsCache.has(key)) missing.push(vid);
                }
                if (missing.length === 0) return;
                const qs = encodeURIComponent(missing.join(','));
                const url = `/pos/variations/bulk?ids=${qs}&location_id=${encodeURIComponent(locationId)}`;
                try {
                    const data = await $.ajax({
                        url: url,
                        method: 'GET',
                        dataType: 'json',
                        delay: 250
                    });
                    if (data && typeof data === 'object') {
                        Object.keys(data).forEach(function(id) {
                            setProductInCache(id, locationId, data[id]);
                        });
                    }
                } catch (e) {
                    // ignore network error; cache remains missing
                }
            }

            let isLoadingTableData = false; // Prevents multiple executions

            async function loadTableData() {
                if (isLoadingTableData) return; // Prevent simultaneous executions
                isLoadingTableData = true;

                const storedArrayData = JSON.parse(localStorage.getItem("pos_form_data_array"));

                // Check if stored data exists
                if (!storedArrayData) {
                    // console.warn("No stored form data found.");
                    isLoadingTableData = false;
                    return;
                }

                console.log("All data:", storedArrayData);
                
                //#JCN add redeemed line discount

                var row_id = storedArrayData.find((item) => item.name === "row_id");
                var row_id = row_id ? row_id.value : null;              
                var rp_redeemed_modal = storedArrayData.find((item) => item.name === "rp_redeemed_modal");
                var rp_redeemed_modal = rp_redeemed_modal ? rp_redeemed_modal.value : 0;
                $("#rp_redeemed_modal").text(__currency_trans_from_en(rp_redeemed_modal, false));
                //console.log('redeemed: ', rp_redeemed_modal);
                
                //console.log('row_id: ' , row_id);
               //var line_discount_amount = storedArrayData.find((item) => item.name === "products["+row_id+"][line_discount_amount]");
/*                var discount_type_modal = line_discount_amount ? line_discount_amount.value : null; */
                //console.log(line_discount_amount);
                //#JCN END 


                const contactIdObj = storedArrayData.find((item) => item.name === "contact_id");
                const contactId = contactIdObj ? contactIdObj.value : null;

                const locationIdObj = storedArrayData.find((item) => item.name === "location_id");
                const location_id = locationIdObj ? locationIdObj.value : null;

                const final_total_item = storedArrayData.find((item) => item.name === "final_total");
                const final_total = final_total_item ? final_total_item.value : null;

                $("#total_payable").text(__currency_trans_from_en(__num_uf(final_total)));

                const discount_type_modal_item = storedArrayData.find((item) => item.name === "discount_type_modal");
                //#JCN To work with Custom Second Display
                const line_discount_amount = discount_type_modal_item;

                const discount_type_modal = discount_type_modal_item ? discount_type_modal_item.value : null;

                const discount_amount_modal_item = storedArrayData.find((item) => item.name ===
                    "discount_amount_modal");
                const discount_amount_modal = discount_amount_modal_item ? discount_amount_modal_item.value : null;

                const price_total_item = storedArrayData.find((item) => item.name === "price_total");
                const price_total = price_total_item ? price_total_item.value : null;

                $(".price_total").text(__currency_trans_from_en(price_total));

                // $("#total_discount").text(__calculate_amount(discount_type_modal, discount_amount_modal,
                //     price_total));

                // Step-by-step discount calculation with logs
            

                const computed_discount = __calculate_amount(
                    discount_type_modal,
                    discount_amount_modal,
                    price_total
                );


                $("#total_discount").text(
                    __currency_trans_from_en(computed_discount)
                );


                const order_tax_item = storedArrayData.find((item) => item.name === "order_tax");
                const order_tax = order_tax_item ? order_tax_item.value : null;


                $("#order_tax").text(__currency_trans_from_en((__num_uf(order_tax))));


                const shipping_charges_amount_item = storedArrayData.find((item) => item.name ===
                    "shipping_charges_amount");
                const shipping_charges_amount = shipping_charges_amount_item ? shipping_charges_amount_item.value : null;

                $("#shipping_charges_amount").text(__currency_trans_from_en(__num_uf(shipping_charges_amount)));


                const total_paying_input_item = storedArrayData.find((item) => item.name === "total_paying_input");
                const total_paying_input = total_paying_input_item ? total_paying_input_item.value : null;

                $(".total_paying").text(__num_uf(total_paying_input));


                const change_return_item = storedArrayData.find((item) => item.name === "change_return");
                const change_return = change_return_item ? change_return_item.value : null;
                $(".change_return_span").text(__num_uf(change_return));

                const in_balance_due_item = storedArrayData.find((item) => item.name === "in_balance_due");
                const in_balance_due = in_balance_due_item ? in_balance_due_item.value : null;
                $(".balance_due").text(__num_uf(in_balance_due));



                // Fetch customer details and update UI
                if (contactId) {
                    const customer = await getCustomerById(contactId);
                    if (customer) {
                        const name = customer.text || customer.name || "";
                        $(".customer_details").html(`<h3>${name}</h3>`);
                    }
                }

                let formattedData = {};
                let formatModifier = [];
                let indexcurrent = 0; //Use to detect the index change for product

                // Parse and format data into a structured object
                storedArrayData.forEach(({
                    name,
                    value
                }) => {
                    let match = name.match(/products\[(\d+)\]\[(.*?)\]/);
                    
                    if (match) {
                        let index = match[1]; // Extract product index (1, 2, etc.)
                        let key = match[2]; // Extract field name (e.g., product_type, unit_price)

                        if (!formattedData[index]) {
                            formattedData[index] = {};
                        }

                        /*#JCN 2025 Add to get the modifiers*/
                        if (key.match(/modifier(.*?)/)) {
                            //console.log('ooooooooooooooo match2 oooooooooooooooo');
                            let stringmatch = 'products\\[('+index+')\\]\\[modifier(.*?)\\]\\[(\\d+)\\]';
                            //let match_mod = name.match(/products\[(\d+)\]\[modifier(.*?)\]\[(\d+)\]/);
                            //let match_mod = name.match(/products\[(1)\]\[modifier(.*?)\]\[(\d+)\]/);
                            
                           let match_mod = name.match(stringmatch);
                           //console.log(match_mod);

                            if(match_mod){
                                let index_mod = match_mod[3]; // Extract modifier index (1, 2, etc.) 

                                if (!formatModifier[index_mod]) {
                                    formatModifier[index_mod] = [];
                                }

                                //To control the index for each product allowed to add the modifiers
                                if (indexcurrent == index){
                                    formatModifier[index_mod][key] = value;
                                    formattedData[index]['modifiers'] = formatModifier;
                                }else{
                                //Change the index this the las modifier
                                    formatModifier[index_mod][key] = value;
                                    formattedData[index]['modifiers'] = formatModifier;
                                //Update indexcurrent the array formatModifier must be empty to get the next
                                    indexcurrent = index;
                                    formatModifier = [];
                                }
                            } 
                        }else{
                                //If isnt a modifier keep in the array
                                formattedData[index][key] = value;
                        }
                        /*#JCN End mofifiers*/
                    }
                });
                
                // Convert object into an array
                const resultArray = Object.values(formattedData).reverse();

                console.log("Formatted Product Data:", resultArray);

                // Select table body
                let tableBody = $("#pos_table tbody");

                // Clear existing table rows
                tableBody.empty();

                // One-time bulk fetch for all needed variations
                const neededVariationIds = [];
                resultArray.forEach(function(prod) {
                    if (prod && prod.variation_id) neededVariationIds.push(prod.variation_id);
                });
                await fetchProductsBulkOnce(neededVariationIds, location_id);

                let totalQuantity = 0;
                let totalItem = 0;

                // Loop through formatted data and append rows to table
                for (let i = 0; i < resultArray.length; i++) {
                    totalItem = totalItem + 1;
                    const product = resultArray[i];
                    const single_product = productsCache.get(makeProductKey(product.variation_id, location_id)) || null;

                    // Determine product image URL
                    let imageUrl = `${base_path}/img/default.png`; // Default image
                    if (single_product && single_product.media && single_product.media.length > 0) {
                        imageUrl = single_product.media[0].display_url;
                    } else if (single_product && single_product.product_image) {
                        imageUrl = `${base_path}/uploads/img/${encodeURIComponent(single_product.product_image)}`;
                    }

                    const quantity = parseFloat(product.quantity) || 0;
                    totalQuantity = totalQuantity + quantity;

                    const unitPrice = __num_uf(product.unit_price_inc_tax);

                    /*Modifiers*/
                        let ModifierHTML ="";
                        //console.log(product.modifiers);
                        if (product.modifiers){
                            product.modifiers.forEach(element => {
                                //console.log(element.modifier)
                                ModifierHTML = ModifierHTML + '<small><br>' + element.modifier_name + '(' + element.modifier_price +  ') </small>';
                            });
                        }
                    /* END */

                    const rowHtml = `
                        <tr>
                            <th>${totalItem}</th>
                            <td class="text-left flex items-center ">
                                <img loading="lazy"style="height:50px;display: inline;margin-left: 3px; border: black;border-radius: 5px; margin-top: 5px; width: 50px;object-fit: cover;" src="${imageUrl}" alt="Product Image" class="w-10 h-10 rounded mr-2"> 
                                <span class="text-nowrap">${single_product ? single_product.product_name : "-"}</span>
                               <span> ${ModifierHTML}</span>
                            </td> 
                            <td class="text-center display_currency" data-currency_symbol="true">${product.unit_price || "0.00"}</td>
                            <td class="text-center">${__currency_trans_from_en(product.line_discount_amount) || "0"}</td>
                            <td class="text-center tw-inline-flex tw-items-center tw-justify-center tw-border-2 tw-rounded-tr-xl tw-w-8 tw-h-6 tw-ms-2  tw-font-semibold tw-bg-gray-200  tw-rounded-full">${product.quantity || "0"}</td>
                            <td class="text-center display_currency" data-currency_symbol="true">${__currency_trans_from_en((quantity * unitPrice).toFixed(2), false)}</td>
                        </tr>
                    `;

                    tableBody.append(rowHtml);
                    
                }
                $(".total_quantity").text(totalQuantity);
                isLoadingTableData = false; // Allow function to execute again
                console.log("Table updated with stored data.");
                __currency_convert_recursively($('.pos_sell'))
            }

            // Load table data initially
            loadTableData();

            // Debounce function to delay execution
            function debounceStorageUpdate() {
                clearTimeout(storageUpdateTimer);
                storageUpdateTimer = setTimeout(() => {
                    console.log("Debounced LocalStorage update: Reloading table...");
                    loadTableData();
                }, 400); // 400ms debounce time
            }
            // Prevent duplicate updates when localStorage changes rapidly
            window.onstorage = debounceStorageUpdate;
        });
    </script>
@endsection

