@extends('layouts.base')

@section('title', 'Bientôt disponible')

@section('content')
    <div class="d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 100px);">
        <img src="{{ asset('images/coming-soon.gif') }}" width="40%">
    </div>
@endsection
