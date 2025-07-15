@extends('layouts.base')

@section('title', 'Formulaire Wildix (ICALL)')
    
@section('content')
<div class="container">
    <a href="{{route('form.pbx-info')}}">Formulaire Wildix (ICALL)</a>
    <div class="row">
        <div class="col-4" style="border:1px solid red">
            <canvas width="300" height="100"></canvas>
        </div>
        <div class="col-4" style="border:1px solid green"></div>
        <div class="col-4" style="border:1px solid black"></div>
    </div>
</div>
@endsection