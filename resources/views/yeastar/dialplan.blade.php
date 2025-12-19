@extends('layouts.base')

@section('title', 'Configuration du dialplan')

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
            <form action="{{ route('yeastar.reset') }}" method="POST">
                @csrf
                <br>
                <button type="submit" style="float:right;" class="btn btn-outline-danger"><i class="fa fa-trash"
                        aria-hidden="true" style="color: darkred;"></i> Vider la session</button>
            </form>

            <div class="text-center col-12 mt-3">
                @include('layouts.header')
            </div>
        </div>

        <form method="POST" action="{{ route('yeastar.dialplan') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-12">
                    <label for="dialplan" class="form-label">Dialplan <span class="required-star">
                            *</span></label>
                    <div class="input-group mb-1">
                        <small><i>NB : Précisez si le répondeur est avec ou sans messagerie.</i></small>
                    </div>
                    <textarea class="form-control" name="dialplan" id="dialplan" cols="600" rows="5"
                        placeholder="Ex : +339XXXXXXXX : 201 - Accueil (20 s.) → CG_ALL (Groupe d'appel) (20 s.) → REPONDEUR sans messagerie"
                        required>{{ old('dialplan', $data['dialplan'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-4">
                    <div class="text-center">
                        <h6>Numéro(s) SDA disponible(s)</h6>
                    </div>
                    @foreach ($data['numeros']['portes'] as $porte)
                        <ul>
                            <li style="list-style: square;">
                                {{ $porte['numero'] }}
                            </li>
                        </ul>
                    @endforeach
                </div>
                <div class="col-4" style="border-left: 1px solid black;border-right: 1px solid black;">
                    <div class="text-center">
                        <h6>Extension(s) disponible(s)</h6>
                    </div>
                    @foreach ($data['extensions'] as $extension)
                        <ul>
                            <li style="list-style: square;">
                                {{ $extension['extension'] . ' - ' . $extension['name'] }}
                            </li>
                        </ul>
                    @endforeach
                </div>
                <div class="col-4">
                    <div class="text-center">
                        <h6>Groupe(s) d'appel(s) disponible(s)</h6>
                    </div>
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
            </div>

            <div class="row mt-3">
                <div class="col-4">
                    <div class="text-center">
                        <h6>File d'attente d'appel</h6>
                    </div>
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

                <div class="col-4" style="border-left: 1px solid black;">
                    <div class="text-center">
                        <h6>SVI disponible</h6>
                    </div>
                    @if (!session('form_yeastar.svi'))
                        Pas de SVI
                    @else
                        {{ $data['svi'] }}
                    @endif
                </div>
            </div>

            <button type="submit" name="previous" value="1" style="float:left;"
                class="btn btn-secondary mt-5">Précédent</button>
            <button type="submit" style="float:right;" class="btn btn-success mt-5">Suivant</button>
        </form>
    </div>
@endsection
