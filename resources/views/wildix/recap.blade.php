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

            <div class="text-center col-12 mt-3">
                @include('layouts.header')
            </div>
        </div>

        {{-- Informations revendeur --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Information(s) générale(s)</h4>
                <br>
                <b>Nom du revendeur :</b> {{ $data['reseller_name'] ?? '' }}
                <br>
                <b>Email destinataire copie récapitulatif :</b> {{ $data['reseller_email'] ?? '' }}
                <br>
            </div>
        </div>

        {{-- Informations client --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Information(s) IPBX</h4>
                <br>
                <b>Nom du client :</b> {{ $data['customer_name'] ?? '' }}
                <br>
                <b>URL PBX :</b> https://{{ $data['url_pbx'] ?? '' }}.wildixin.com/
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

                            <td><input type="number" value="{{ $extension['extension'] }}"
                                    class="no-disabled form-control" disabled></td>

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

        {{-- Équipements WILDIX --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Équipements WILDIX</h4>
                <br>
                @if (!session('form_wildix.devices'))
                    <h6>Pas d'équipements</h6>
                @else
                    @foreach ($data['devices'] as $index => $device)
                        Nom du matériel : <b>{{ $device['device_name'] }}</b>
                        @if (isset($device['extension']))
                            <br>
                            Extension associée : <b>{{ $device['extension'] }}</b>
                        @endif
                        <br><br>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Callgroups --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Groupe(s) d'appel</h4>
                <br>
                @if (!session('form_wildix.callgroups'))
                    <h6>Pas de groupe(s) d'appel</h6>
                @else
                    @foreach ($data['callgroups'] as $index => $callgroup)
                        Nom du groupe : {{ $callgroup['name'] }}
                        <br>
                        Type du groupe : {{ $callgroup['type'] }}
                        <br>
                        Extensions associées :
                        @foreach ($callgroup['ext'] as $extension)
                            @if ($callgroup['type'] === 'all_10')
                                {{ $extension }}
                                @if (!$loop->last)
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
                @endif
            </div>
        </div>

        {{-- Timetable --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Heure(s) d'ouverture (H.O.)</h4>
                <br>
                @if (!session('form_wildix.timetable_ho'))
                    <textarea class="form-control" disabled cols="600" rows="5"></textarea>
                @else
                    <textarea class="form-control" disabled cols="600" rows="5">{{ $data['timetable_ho'] ?? '' }}</textarea>
                @endif
            </div>
        </div>

        {{-- SVI --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>SVI</h4>
                <br>
                @if (!session('form_wildix.svi'))
                    <textarea class="form-control" disabled cols="600" rows="5">Pas de svi</textarea>
                @else
                    <textarea class="form-control" disabled cols="600" rows="5">{{ session('form_wildix.svi') }}</textarea>
                @endif
            </div>
        </div>

        {{-- Dialplan --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Dialplan(s) :</h4>
                <br>
                <textarea class="form-control" cols="600" rows="5" disabled>{{ $data['dialplan'] ?? '' }}</textarea>
            </div>
        </div>

        {{-- Informations supplémentaires et remarques --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Information(s) supplémentaire(s) et remarque(s)</h4>
                <br>
                @if (!session('form_wildix.infos_remarques'))
                    <textarea class="form-control" cols="600" rows="5" disabled></textarea>
                @else
                    <textarea class="form-control" cols="600" rows="5" disabled>{{ $data['infos_remarques'] ?? '' }}</textarea>
                @endif
            </div>
        </div>

        <form action="{{ route('wildix.export') }}" method="GET">
            <br>
            <div class="row mt-5 ">
                <div style="display:none">
                    <input type="text" name="website" value="">
                </div>
                <div class="col-10">
                    <input type="checkbox" name="validation" id="validation" required><label for="validation">&ensp;<b>Je
                            certifie l'exactitude des données transmises. </b><span class="required-star">*</span></label>
                </div>
            </div>

            <br>

            <a href="{{ route('wildix.infos') }}" style="float:left;" class="btn btn-secondary mt-5">Précédent</a>
            <button type="submit" style="float:right;" class="btn btn-success mt-5 mb-5">Envoyer les
                données</button>
        </form>

    </div>
@endsection
