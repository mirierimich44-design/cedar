@extends('layouts.app')
@section('title', 'MOH 705A Report')

@section('content')
<section class="content-header">
    <h1>MOH 705A - Outpatient Summary (Under 5 Years)</h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Period: {{ $start_date }} to {{ $end_date }}</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Disease / Diagnosis</th>
                        <th>Number of Cases</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total_cases = 0; @endphp
                    @foreach($data as $row)
                        <tr>
                            <td>{{ $row->diagnosis }}</td>
                            <td>{{ $row->total }}</td>
                        </tr>
                        @php $total_cases += $row->total; @endphp
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray">
                        <th>TOTAL CASES</th>
                        <th>{{ $total_cases }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="box-footer no-print">
            <button type="button" class="btn btn-default" onclick="window.print();">
                <i class="fa fa-print"></i> Print Report
            </button>
        </div>
    </div>
</section>
@endsection
