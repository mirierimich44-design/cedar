@extends('layouts.app')
@section('title', 'Prescriptions')

@section('content')
<section class="content-header">
    <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">DDA Prescriptions</h1>
</section>

<section class="content">
    @if(session('status'))
        <div class="alert alert-{{ session('status')['success'] ? 'success' : 'danger' }}">{{ session('status')['msg'] }}</div>
    @endif

    @component('components.widget', ['class' => 'box-primary', 'title' => 'Prescription Records'])
        <div class="row" style="margin-bottom:12px">
            <div class="col-md-12">
                <a href="{{ route('dda.prescriptions.create') }}" class="btn btn-danger">
                    <i class="fa fa-upload"></i> Upload New Prescription
                </a>
            </div>
        </div>

        @if($prescriptions->total() === 0)
            <div class="alert alert-info">No prescriptions uploaded yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Prescriber</th>
                            <th>Hospital</th>
                            <th>Uploaded By</th>
                            <th>Prescription</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prescriptions as $p)
                        <tr>
                            <td>{{ $p->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y H:i') }}</td>
                            <td>{{ $p->patient_name ?? '—' }}</td>
                            <td>{{ $p->prescriber_name ?? '—' }}</td>
                            <td>{{ $p->prescriber_hospital ?? '—' }}</td>
                            <td>
                                @php $uploader = \App\User::find($p->created_by); @endphp
                                {{ $uploader ? $uploader->first_name . ' ' . $uploader->last_name : '—' }}
                            </td>
                            <td>
                                @if($p->image_path)
                                    <a href="{{ route('dda.prescriptions.view', $p->id) }}" target="_blank" class="btn btn-xs btn-info">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                @else
                                    <span class="text-muted">No image</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $prescriptions->links() }}
        @endif
    @endcomponent
</section>
@endsection
