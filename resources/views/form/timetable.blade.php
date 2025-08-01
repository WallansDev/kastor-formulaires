@extends('layouts.base')

@section('title', 'Heures d\'ouverture')

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
            <form action="{{ route('form.reset') }}" method="POST">
                @csrf
                <br>
                <button type="submit" style="float:right;" class="btn btn-outline-danger"><i class="fa fa-trash"
                        aria-hidden="true" style="color: darkred;"></i> Vider la session</button>
            </form>

            <div class=" text-center col-12 mt-3">
                @include('form.header')
            </div>
        </div>

        <form method="POST" action="{{ route('form.timetable') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-5">
                    <label for="timetable_ho" class="form-label">Heures d'ouverture (H.O.)</label>
                    <div class="input-group mb-1">
                        <small><i>NB : HO = Heures Ouvrées (ouverture) ≠ HNO = Heure Non Ouvrée (fermeture)</i></small>
                    </div>
                    <textarea class="form-control" name="timetable_ho" id="timetable_ho" cols="600" rows="3"
                        placeholder="Ex : Lun au Ven. : 8h à 12h / 14h à 18h">{{ old('timetable_ho', $data['timetable_ho'] ?? '') }}</textarea>
                </div>
            </div>
            <button type="submit" style="float:right;" class="btn btn-success">Suivant</button>
        </form>
    </div>
@endsection
