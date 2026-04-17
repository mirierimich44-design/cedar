@extends('layouts.app')
@section('title', 'DDA Drug List')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">DDA Drug List</h1>
</section>

<section class="content">
    @if(session('status'))
        <div class="alert alert-{{ session('status')['success'] ? 'success' : 'danger' }}">{{ session('status')['msg'] }}</div>
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' => 'Controlled Substances Register'])
        <div class="row" style="margin-bottom:12px">
            <div class="col-md-12">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addDrugModal">
                    <i class="fa fa-plus"></i> Add Drug
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dda_drugs_table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Drug Name</th>
                        <th>Class</th>
                        <th>Schedule</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($drugs as $drug)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td><strong>{{ $drug->name }}</strong></td>
                        <td>
                            @php
                                $colors = ['Opioid'=>'danger','Benzodiazepine'=>'warning','Barbiturate'=>'primary','Dissociative'=>'info','Sedative'=>'default','Anticonvulsant'=>'success','Precursor'=>'default'];
                                $color = $colors[$drug->class] ?? 'default';
                            @endphp
                            <span class="label label-{{ $color }}">{{ $drug->class }}</span>
                        </td>
                        <td><span class="label label-default">Schedule {{ $drug->schedule }}</span></td>
                        <td>{{ $drug->description ?? '—' }}</td>
                        <td>
                            @if($drug->is_active)
                                <span class="label label-success">Active</span>
                            @else
                                <span class="label label-danger">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-xs btn-info edit-drug-btn"
                                data-id="{{ $drug->id }}"
                                data-name="{{ $drug->name }}"
                                data-class="{{ $drug->class }}"
                                data-schedule="{{ $drug->schedule }}"
                                data-description="{{ $drug->description }}"
                                data-active="{{ $drug->is_active }}"
                                data-toggle="modal" data-target="#editDrugModal">
                                <i class="fa fa-edit"></i> Edit
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endcomponent
</section>

{{-- Add Drug Modal --}}
<div class="modal fade" id="addDrugModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add DDA Drug</h4>
            </div>
            {!! Form::open(['route' => 'dda.drugs.store', 'method' => 'post']) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label>Drug Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Class *</label>
                    <select name="class" class="form-control" required>
                        @foreach(\App\DdaDrug::classList() as $c)
                            <option>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Schedule *</label>
                    <select name="schedule" class="form-control" required>
                        @foreach(\App\DdaDrug::scheduleList() as $s)
                            <option>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

{{-- Edit Drug Modal --}}
<div class="modal fade" id="editDrugModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Edit DDA Drug</h4>
            </div>
            {!! Form::open(['route' => ['dda.drugs.update', 0], 'method' => 'post', 'id' => 'editDrugForm']) !!}
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label>Drug Name *</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Class *</label>
                    <select name="class" id="edit_class" class="form-control" required>
                        @foreach(\App\DdaDrug::classList() as $c)
                            <option>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Schedule *</label>
                    <select name="schedule" id="edit_schedule" class="form-control" required>
                        @foreach(\App\DdaDrug::scheduleList() as $s)
                            <option>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="is_active" id="edit_active" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script>
$(document).ready(function() {
    $('#dda_drugs_table').DataTable({pageLength: 25});

    $('.edit-drug-btn').click(function() {
        var btn = $(this);
        var id = btn.data('id');
        $('#edit_name').val(btn.data('name'));
        $('#edit_class').val(btn.data('class'));
        $('#edit_schedule').val(btn.data('schedule'));
        $('#edit_description').val(btn.data('description'));
        $('#edit_active').val(btn.data('active'));
        var action = "{{ route('dda.drugs.update', ':id') }}".replace(':id', id);
        $('#editDrugForm').attr('action', action);
    });
});
</script>
@endsection
