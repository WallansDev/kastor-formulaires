@extends('layouts.base')

@section('title', 'SVI')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 mt-1">
                @if (session('info'))
                    <div class="alert alert-info">
                        {{ session('info') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
            <form action="{{ route('wildix.reset') }}" method="POST">
                @csrf
                <br>
                <button type="submit" style="float:right;" class="btn btn-outline-danger"><i class="fa fa-trash"
                        aria-hidden="true" style="color: darkred;"></i> Vider la session</button>
            </form>

            <div class=" text-center col-12 mt-3">
                @include('layouts.header')
            </div>
        </div>

        <form method="POST" action="{{ route('wildix.svi') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-6">
                    <label for="svi" class="form-label">SVI (Serveur Vocal Interactif)</label>
                    <div class="input-group mb-1">
                        {{-- <small><i>NB : HO = Heures Ouvrées (ouverture) ≠ HNO = Heure Non Ouvrée (fermeture)</i></small> --}}
                    </div>
                    <textarea class="form-control" name="svi" id="svi" cols="800" rows="10"
                        placeholder="Ex: &#10;Choix 1 : Accueil &#10;Choix 2 : Commercial &#10;Choix 3 : Technique &#10;...">{{ old('svi', $data['svi'] ?? '') }}</textarea>
                </div>
            </div>
            <button type="submit" style="float:right;" class="btn btn-success">Suivant</button>
        </form>
    </div>
@endsection
