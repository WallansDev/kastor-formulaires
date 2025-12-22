@extends('layouts.base')

@section('title', 'Formulaires - KASTOR')

@section('content')
    <div class="container">
        <div class="col-12 mt-1">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </div>
    {{-- Petit carré cliquable en bas à droite --}}
    <a href="{{ route('easter-egg') }}" class="easter-egg-square" aria-label="Accès spécial"></a>
@endsection
