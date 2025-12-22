@extends('layouts.base')

@section('title', 'Easter Egg 1')

@section('content')
    <div class="container mt-5 text-center">
        <h1 class="mb-4">🎉 Bravo !</h1>
        <p>Vous avez découvert la page cachée.</p>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Retour à l'accueil</a>
    </div>
@endsection
