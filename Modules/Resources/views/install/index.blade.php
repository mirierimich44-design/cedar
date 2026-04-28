@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Install MadeToOrder Module</h3>
    <form method="post" action="{{ route('made_to_order.install.run') }}">
        @csrf
        <button class="btn btn-success">Install</button>
    </form>
</div>
@endsection
