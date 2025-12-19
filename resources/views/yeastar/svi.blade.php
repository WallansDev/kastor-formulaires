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
            @include('layouts.header')
        </div>

        <form method="POST" action="{{ route('yeastar.svi') }}">
            @csrf
            <div class="row">
                <div class="col-6">
                    <label for="svi" class="form-label">SVI (Serveur Vocal Interactif)</label>
                    <div class="input-group mb-1">
                        {{-- <small><i>NB : HO = Heures Ouvrées (ouverture) ≠ HNO = Heure Non Ouvrée (fermeture)</i></small> --}}
                    </div>
                    <textarea class="form-control" name="svi" id="svi" cols="800" rows="10"
                        placeholder="Ex: &#10;Choix 1 : Accueil &#10;Choix 2 : Commercial &#10;Choix 3 : Technique &#10;...">{{ old('svi', $data['svi'] ?? '') }}</textarea>
                </div>
            </div>

            <button type="submit" name="previous" value="1" style="float:left;"
                class="btn btn-secondary mt-5">Précédent</button>
            <button type="submit" style="float:right;" class="btn btn-success mt-5">Suivant</button>
        </form>
    </div>
@endsection
