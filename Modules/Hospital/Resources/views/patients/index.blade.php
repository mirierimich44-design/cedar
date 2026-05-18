@extends('layouts.app')

@section('title', 'Patients')

@section('content')
    <section class="content-header">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            <i class="fa fa-users"></i> Patients
        </h1>
    </section>

    <section class="content">
        @component('components.widget')
            <div class="box-tools tw-flex tw-justify-end tw-gap-2.5 tw-mb-4">
                @if(auth()->user()->can('hospital.receptionist') || auth()->user()->can('hospital.admin'))
                    <a href="{{ route('hospital.patients.create') }}"
                       class="tw-dw-btn tw-bg-gradient-to-r tw-from-indigo-600 tw-to-blue-500 tw-font-bold tw-text-white tw-border-none tw-rounded-full">
                        <i class="fa fa-plus"></i> New Patient
                    </a>
                @endif
            </div>

            <table class="table table-bordered table-striped" id="patients_table">
                <thead>
                    <tr>
                        <th>Patient No</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Gender</th>
                        <th>Blood Group</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        @endcomponent
    </section>
@endsection

@section('javascript')
<script>
$(document).ready(function () {
    var table = $('#patients_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('hospital.patients.index') }}'
        },
        columns: [
            { data: 'patient_no',   name: 'patient_no' },
            { data: 'full_name',    name: 'first_name' },
            { data: 'phone',        name: 'phone' },
            { data: 'gender',       name: 'gender' },
            { data: 'blood_group',  name: 'blood_group' },
            { data: 'action',       name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']]
    });
});
</script>
@endsection
