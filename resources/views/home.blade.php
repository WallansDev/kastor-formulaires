@extends('layouts.base')

@section('title', 'Formulaires - KASTOR')
    
@section('content')
<div class="container">
    
    <div class="row mt-3">
        <div class="col-4"></div>
        <div class="col-4">
        <a href="{{route('form.pbx-info')}}"><img src="{{asset('images/formulaire.jpg')}}" class="img-thumbnail" alt="...">
        <h3 class="mt-1" style="text-align:center;">Formulaire Wildix</h3></a>
        </div>
    </div>
</div>
@endsection