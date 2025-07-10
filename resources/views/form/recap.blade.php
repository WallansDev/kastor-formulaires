@extends('layouts.base')

@section('title', 'Récapitulatif des éléments')

@section('head-content')
    <style>
        .no-disabled {
            background-color: white !important;
            text-align: center;
        }

        h4 {
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="container">

        {{-- DEBUG --}}
        <a href="{{ route('debug.session') }}">Dump Session</a>

        <div class="row">
            <form action="{{ route('form.reset') }}" method="POST">
                @csrf
                <button type="submit" style="float:right;" class="btn btn-outline-danger"><i class="fa fa-trash"
                        aria-hidden="true" style="color: darkred;"></i> Vider la session</button>
            </form>

            <div class="text-center col-12 mt-3">
                @include('form.header')
            </div>
        </div>

        {{-- Informations client --}}
        Nom client
        <div class="row mt-5">
            <div class="col-12">
                <h4>Informations du client</h4>
                <br>
                <b>Nom du client :</b> {{ $data['customer_name'] ?: '' }}
                <br>
                <b>URL IPBX :</b> https://{{ $data['url_pbx'] ?: '' }}.wildixin.com/
            </div>
        </div>

        {{-- Numéros portés et provisoires --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Numéro(s) porté(s)/créé(s) & provisoire(s)</h4>
                <br>
                @foreach ($data['numeros']['portes'] as $index => $porte)
                    <li>
                        Porté / Créé : {{ $porte['numero'] }}
                        @if ($porte['provisoire'])
                            ➔ Provisoire : {{ $porte['provisoire'] }}
                        @else
                            ➔ Pas de provisoire
                        @endif
                    </li>
                @endforeach
            </div>
        </div>

        {{-- Extensions --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Extension(s)</h4>
                <br>
            </div>
            <table class="table table-bordered">
                <thead style="text-align: center">
                    <tr>
                        <th>#Extension <span class="required-star">*</span></th>
                        <th>Nom affiché <span class="required-star">*</span></th>
                        <th>Email</th>
                        <th>#Présenté (appel sortant) <span class="required-star">*</span></th>
                        <th>Langue</th>
                        <th>Licence <span class="required-star">*</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['extensions'] as $index => $extension)
                        <tr>

                            <td><input type="number" value="{{ $extension['extension'] }}" class="no-disabled form-control"
                                    disabled></td>

                            <td><input type="text" value="{{ $extension['name'] }}" class="form-control no-disabled"
                                    disabled></td>

                            <td><input type="text" value="{{ $extension['email'] }}" class="form-control no-disabled"
                                    disabled></td>

                            <td><input type="text" value="{{ $extension['numPorte'] }}" class="form-control no-disabled"
                                    disabled></td>

                            <td><input type="text" value="{{ strtoupper($extension['language']) }}"
                                    class="form-control no-disabled" disabled></td>

                            <td><input type="text" value="{{ ucfirst($extension['licence']) }}"
                                    class="form-control no-disabled" disabled></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Callgroups --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Groupe(s) d'appel</h4>
                <br>
                @foreach ($data['callgroups'] as $index => $callgroup)
                    Nom du groupe : {{ $callgroup['name'] }}
                    <br>
                    Type du groupe : {{ $callgroup['type'] }}
                    <br>
                    Extensions associées :
                    @foreach ($callgroup['ext'] as $extension)
                        @if ($callgroup['type'] === 'all_10')
                            @if (!$loop->last)
                                +
                            @endif
                            {{ $extension }}@if (!$loop->last)
                                +
                            @endif
                        @elseif ($callgroup['type'] === 'linear')
                            {{ $extension }}@if (!$loop->last)
                                ->
                            @endif
                        @endif
                    @endforeach
                    <br><br>
                @endforeach
            </div>
        </div>

        {{-- Timetable --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Heure(s) d'ouverture (H.O.)</h4>
                <br>
                <textarea class="form-control" cols="600" rows="5">{{ $data['timetable_ho'] }}</textarea>
            </div>
        </div>

        {{-- SVI --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>SVI</h4>
                <br>
                @foreach ($data['svi_options'] as $option)
                    {{ 'Choix : ' . $option['ordre'] . ' = ' . $option['nom'] }}
                    <br>
                @endforeach
            </div>
        </div>

        {{-- Dialplan --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Dialplan(s) :</h4>
                <br>
                <textarea class="form-control" cols="600" rows="5">{{ $data['dialplan'] }}</textarea>
            </div>
        </div>

        {{-- Informations supplémentaires et remarques --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Information(s) supplémentaire(s) et remarque(s)</h4>
                <br>
                <textarea class="form-control" cols="600" rows="5">{{ $data['infos_remarques'] }}</textarea>
            </div>
        </div>

        <form action="{{ route('export') }}" method="GET">
            <br>
            <div class="row mt-5 mb-5">
                <div style="display:none">
                    <input type="text" name="website" value="">
                </div>
                <div class="col-10">
                    <input type="checkbox" name="validation" id="validation" required><label for="validation">&ensp;<b>Je
                            certifie l'exactitude des données transmises. </b><span class="required-star">*</span></label>
                </div>
                <div class="col-2 d-flex justify-content-end">
                    <button class="btn btn-outline-info" type="submit">Envoyer les
                        données</button>
                </div>
            </div>
        </form>

    </div>
@endsection
