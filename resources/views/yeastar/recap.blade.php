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

    <div class="container mb-5">
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
                @if ($data['url_pbx'] === '')
                    <b>Pas d'URL PBX indiqué</b>
                @else
                    <b>URL PBX :</b> https://{{ $data['url_pbx'] }}.vokalise.fr
                @endif
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
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Équipements yeastar --}}
        {{-- <div class="row mt-5">
            <div class="col-12">
                <h4>Équipements yeastar</h4>
                <br>
                @if (!session('form_yeastar.devices'))
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
        </div> --}}

        {{-- Callgroups --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Groupe(s) d'appel</h4>
                <div class="col-4">
                    @if (!empty($data['callgroups']))
                        @foreach ($data['callgroups'] as $index => $group)
                            <div class="mb-3 p-2 border rounded">
                                <strong>{{ $group['name'] }}</strong> (Stratégie : {{ $group['type'] }})
                                @if (($group['type'] ?? null) === 'memory_hunt' && !empty($group['ring_timeout']))
                                    <div class="text-muted small">délai d'attente : {{ $group['ring_timeout'] }}s</div>
                                @endif

                                @if (!empty($group['ext']))
                                    <ul class="mt-2">
                                        @foreach ($group['ext'] as $extIndex => $ext)
                                            @php
                                                $displayMeta = '';
                                                if (($group['type'] ?? null) === 'custom') {
                                                    $settings = $group['ext_settings'][$ext] ?? null;
                                                    if ($settings) {
                                                        $parts = [];
                                                        if (
                                                            isset($settings['ring_delay']) &&
                                                            $settings['ring_delay'] !== null &&
                                                            $settings['ring_delay'] !== ''
                                                        ) {
                                                            $parts[] = 'délai : ' . $settings['ring_delay'] . 's';
                                                        }
                                                        if (
                                                            isset($settings['ring_timeout']) &&
                                                            $settings['ring_timeout'] !== null &&
                                                            $settings['ring_timeout'] !== ''
                                                        ) {
                                                            $parts[] =
                                                                "délai d'attente : " . $settings['ring_timeout'] . 's';
                                                        }
                                                        $displayMeta = implode(' | ', $parts);
                                                    }
                                                }
                                            @endphp
                                            <li>
                                                {{ $ext }}
                                                @if (!empty($displayMeta))
                                                    <span class="text-muted"> — {{ $displayMeta }}</span>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">Aucune extension assignée.</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p>Aucun groupe d’appel pour le moment.</p>
                    @endif
                </div>
                {{-- @if (!session('form_yeastar.callgroups'))
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
                @endif --}}
            </div>
        </div>

        {{-- Queues --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Queue(s) d'appel</h4>
                <div class="col-4">
                    @if (!empty($data['queues']))
                        @foreach ($data['queues'] as $index => $queue)
                            <div class="mb-3 p-2 border rounded">
                                <strong>{{ $queue['name'] }}</strong>
                                @if (!empty($queue['strategy']))
                                    <div class="text-muted small">stratégie : {{ $queue['strategy'] }}</div>
                                @endif

                                @if (!empty($queue['ext']))
                                    <ul class="mt-2">
                                        @foreach ($queue['ext'] as $extIndex => $ext)
                                            <li>
                                                {{ $ext }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">Aucune extension assignée.</p>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <p>Aucune file d'attente pour le moment.</p>
                    @endif
                </div>
                {{-- @if (!session('form_yeastar.queues'))
                    <h6>Pas de queue(s) d'appel</h6>
                @else
                    @foreach ($data['queues'] as $index => $queue)
                        Nom du groupe : {{ $queue['name'] }}
                        <br>
                        Extensions associées :
                        @foreach ($queue['ext'] as $extension)
                            {{ $extension }}
                            @if (!$loop->last)
                                +
                            @endif
                        @endforeach
                        <br><br>
                    @endforeach
                @endif --}}
            </div>
        </div>

        {{-- Timetable --}}
        <div class="row mt-5">
            <div class="col-12">
                <h4>Heure(s) d'ouverture (H.O.)</h4>
                <br>
                @if (!session('form_yeastar.timetable_ho'))
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
                @if (!session('form_yeastar.svi'))
                    <textarea class="form-control" disabled cols="600" rows="5">Pas de svi</textarea>
                @else
                    <textarea class="form-control" disabled cols="600" rows="5">{{ session('form_yeastar.svi') }}</textarea>
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
                @if (!session('form_yeastar.infos_remarques'))
                    <textarea class="form-control" cols="600" rows="5" disabled></textarea>
                @else
                    <textarea class="form-control" cols="600" rows="5" disabled>{{ $data['infos_remarques'] ?? '' }}</textarea>
                @endif
            </div>
        </div>

        <form action="{{ route('yeastar.export') }}" method="GET">
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

            <a href="{{ route('yeastar.infos') }}" style="float:left;" class="btn btn-secondary mt-5">Précédent</a>
            <button type="submit" style="float:right;" class="btn btn-success mt-5 mb-5">Envoyer les
                données</button>
        </form>
    </div>
@endsection
