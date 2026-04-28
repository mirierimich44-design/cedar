@extends('layouts.app')
@section('css')
  <style>
    #products_table tr{
      cursor: pointer;
      transition: .3s;
    }
    #products_table tr:hover{
      background-color: #cfcdcd;
    }
  </style>
@endsection
@section('title', __('inventoryemptying::app.emptyQty'))
@section('content')
	<!-- Content Header (Page header) -->
	<section class="content-header no-print">
	    <h1>
	    	@lang('inventoryemptying::app.emptyQty')
	    </h1>
	</section>
	<!-- Main content -->
	<section class="content no-print">
		<div class="box box-solid">
			<div class="box-header with-border">
				<h5 class="box-title">
					@lang('inventoryemptying::app.pervious_transaction')
				</h5>
				
				<div class="box-tools pull-right">
					<button type="button" class="btn btn-sm btn-primary" id="inventory_empty" data-toggle="modal" data-target="#choose_locations">
					    <i class="fa fa-plus"></i>
					    @lang('inventoryemptying::app.emptyQty')
					</button>
				</div>
				
			</div>
			<div class="box-body">
				<div class="table-responsive">
					<table class="table table-bordered dataTable table-striped" id="products_table" style="">
						<thead>
							<tr>
                                <th>#</th>
                                <th>@lang('inventoryemptying::app.created_by')</th>
                                <th>@lang('inventoryemptying::app.date')</th>
							</tr>
						</thead>
						<tbody>
                            @forelse($empties as $empty)
                                <tr data-emid="{{$empty->id}}">
                                    <td>{{$loop->remaining + 1}}</td>
                                    <td>{{$empty->createdBy->first_name . '|'. $empty->createdBy->username}}</td>
                                    <td>{{$empty->created_at}}</td>
                                </tr>
                            @empty
                           <tr>
                                <td align="center" colspan="3">@lang('inventoryemptying::app.no_old')</td>
                            </tr>
                            @endforelse
                        </tbody>
					</table>
				</div>
			</div> 
		</div>
	</section>
<div class="modal fade" id="choose_locations" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">    <span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title">اختر فروع للحذف<h5>
      </div>
      <div class="modal-body">
        <div class="col-12">
                <select class="form-control" multiple id="location_id">
                  @foreach($locations as $location)
                    <option value="{{Crypt::encryptString($location->id)}}">{{$location->name}}</option>
                  @endforeach
                </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('inventoryemptying::app.close')</button>
        <button type="button" class="btn btn-primary" id="inventory_empty_submit">@lang('inventoryemptying::app.empty_btn')</button>
      </div>
    </div>
  </div>
</div>
<div class="modal" tabindex="-1" id="showProducts">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">@lang('inventoryemptying::app.showModal') - <span id="editProductModal_modal_id"></span></h4>

          </div>
          <div class="modal-body">
            
            
          </div>
          <div class="modal-footer">
              <button type="button" class="btn btn-secondary" aria-label="@lang('inventoryemptying::app.close')" data-dismiss="modal" id="editProductModalClose">@lang('inventoryemptying::app.close')</button>

            </div>
      </div>
  </div>
</div>

@stop
@section('javascript')

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>


<script type="text/javascript">
	$(document).ready(function () {
        $.ajaxSetup({ 
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                            }
                        });

        $('#inventory_empty_submit').click(function (e){
            e.preventDefault();
           if($('#location_id').val() == '') {return 0}
            swal({
              title: "{{   __('inventoryemptying::app.are_u_sure')  }}",
              text:  "{{   __('inventoryemptying::app.remove_alert')  }}",
              icon: "warning",
              buttons: true,
              dangerMode: true,
            })
            .then((willDelete) => {
              if (willDelete) {
                  $.ajax({
                      url:"{{url('inventoryemptying/inventoryemptying/')}}",
                      method:'POST',
                      data:{
                          location_id:$('#location_id').val()
                  },
                      success:function (res){
                             swal("{{   __('inventoryemptying::app.success')  }}", {
                                  icon: "success",
                                });
                      },
                      error:function(errs){
                          console.log(errs)
                      }
                  })
               
              }
              
            });
        })
        $('#products_table tr').click(function (e){
          e.preventDefault();
          let attrId = $(this).attr('data-emid');
          $.ajax({
            url:"{{ action('\Modules\InventoryEmptying\Http\Controllers\InventoryEmptyingController@show','')}}/"+attrId,
            method:'GET',
            success:function (res){
              console.log(res);
              $('#showProducts .modal-body').html(res);
              $('#showProducts').modal('show')
              $('#editProductModal_modal_id').text(attrId)
            },
            error:function(errs){
              console.log(errs);
            }
          });
        });
	});
	
</script> 
@endsection