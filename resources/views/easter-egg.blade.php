@extends('layouts.base')

@section('title', 'Easter Egg 1')

@section('content')
    <div class="container mt-5 text-center">
        <h1 class="mb-4">🎉 Bravo !</h1>
        <p>Vous avez découvert la page cachée.</p>
        <div>
            <a href="/gif/gig-DSV7NE" title="gig"><img src="https://i.makeagif.com/media/12-22-2025/DSV7NE.gif"
                    alt="gig" width="50%"></a>
        </div>
        <a href="{{ route('home') }}" class="btn btn-primary mt-3">Retour à l'accueil</a>
    </div>
@endsection
