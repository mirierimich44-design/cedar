@extends('layouts.app')
@section('title', 'Patient EHR Timeline')

@section('content')
<section class="content-header">
    <h1>Electronic Health Record: {{ $patient->name }}</h1>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <!-- The time line -->
            <ul class="timeline">
                @php $last_date = ''; @endphp
                @foreach($timeline as $item)
                    @php 
                        $current_date = \Carbon\Carbon::parse($item['date'])->format('d M, Y');
                    @endphp
                    
                    @if($current_date != $last_date)
                        <li class="time-label">
                            <span class="bg-gray">{{ $current_date }}</span>
                        </li>
                        @php $last_date = $current_date; @endphp
                    @endif

                    <li>
                        <i class="fa {{ $item['icon'] }} {{ $item['bg'] }}"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fa fa-clock-o"></i> {{ \Carbon\Carbon::parse($item['date'])->format('h:i A') }}</span>
                            <h3 class="timeline-header"><strong>{{ $item['type'] }}</strong>: {{ $item['title'] }}</h3>
                            <div class="timeline-body">
                                {{ $item['description'] }}
                                <br>
                                <small class="text-muted">By: {{ $item['provider'] }}</small>
                            </div>
                        </div>
                    </li>
                @endforeach
                <li>
                    <i class="fa fa-clock-o bg-gray"></i>
                </li>
            </ul>
        </div>
    </div>
</section>
@endsection
