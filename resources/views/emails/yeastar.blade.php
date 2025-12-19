{{-- @php
    $customer_name = session('form_yeastar.customer_name');
    $reseller_name = session('form_yeastar.reseller_name');
    $reseller_email = session('form_yeastar.reseller_email');
    $urlPbx = session('form_yeastar.url_pbx');
    $portes = session('form_yeastar.numeros.portes', []);
    $extensions = session('form_yeastar.extensions');
    $callGroups = session('form_yeastar.callgroups');
    $queues = session('form_yeastar.queues');
    $timetable_ho = session('form_yeastar.timetable');
    $svi = session('form_yeastar.svi');
    $dialplan = session('form_yeastar.dialplan');
    // $devices = session('form_yeastar.devices');
    $infos_remarques = session('form_yeastar.infos_remarques');
@endphp --}}


<h3>Information(s) générale(s)</h3>
<strong style="font-size: 13px">Nom du revendeur :</strong> {{ $reseller_name }}
<br>
<strong style="font-size: 13px">Email du revendeur :</strong> {{ $reseller_email }}
<br><br>

<h3>Information(s) du client :</h3>
<strong style="font-size: 13px">Nom du client :</strong> {{ $customer_name }}
<br>
@if ($urlPbx === '')
    <strong style="font-size: 13px">Pas d'URL PBX indiqué</strong>
@else
    <strong style="font-size: 13px">URL PBX :</strong> https://{{ $urlPbx }}.vokalise.fr
@endif
<br><br>

<h3>Numéro(s) porté(s)/créé(s) & provisoire(s) :</h3>
@foreach ($portes as $porte)
    <strong>Porté / Créé :</strong> {{ $porte['numero'] }} =>
    <strong>Provisoire :</strong> {{ $porte['provisoire'] ?? 'Aucun' }}
    @if (!$loop->last)
        <br>
    @endif
@endforeach
<br><br>

<h3>Extension(s) :</h3>
<table style="padding: 10px; border: 1px solid black; border-collapse: collapse;">
    <thead style="text-align: center; background-color: lightgray;">
        <tr>
            <th
                style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif;">
                Extension</th>
            <th
                style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif;">
                Nom affiché</th>
            <th
                style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif;">
                Email</th>
            <th
                style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif;">
                Office</th>
            <th
                style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif;">
                Langue</th>

        </tr>
    </thead>
    <tbody style="text-align: center;" id="tableBody">

        @foreach ($extensions as $extension)
            <tr>
                <td
                    style="padding: 10px; border: 1px solid black; border-collapse: collapse;  font-family:'Franklin Gotdic Medium', 'Arial Narrow', Arial, sans-serif;">
                    {{ $extension['extension'] }}</td>
                <td
                    style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family:'Franklin Gotdic Medium', 'Arial Narrow', Arial, sans-serif;">
                    {{ $extension['name'] }}</td>
                <td
                    style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family:'Franklin Gotdic Medium', 'Arial Narrow', Arial, sans-serif;">
                    {{ $extension['email'] ?? '--' }}</td>
                <td
                    style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family:'Franklin Gotdic Medium', 'Arial Narrow', Arial, sans-serif;">
                    {{ $extension['numPorte'] }}</td>
                <td
                    style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family:'Franklin Gotdic Medium', 'Arial Narrow', Arial, sans-serif;">
                    {{ strtoupper($extension['language']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<br><br>

<h3>Groupe(s) d'appel :</h3>
@if (!$callGroups || is_null($callGroups))
    Aucun groupe d’appel pour le moment.
@else
    @foreach ($callGroups as $index => $group)
        <div class="mb-3 p-2 border rounded">
            Nom du groupe : <strong>{{ $group['name'] }}</strong>
            <br>
            Stratégie : {{ $group['type'] }}
            @if (($group['type'] ?? null) === 'memory_hunt' && !empty($group['ring_timeout']))
                (délai d'attente : {{ $group['ring_timeout'] }}s)
            @endif
            <br>
            Extension(s) associée(s) :
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
                                        $parts[] = "délai d'attente : " . $settings['ring_timeout'] . 's';
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
                        <br>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Aucune extension assignée.</p>
            @endif
        </div>
    @endforeach
@endif
<br>

<h3>Queue(s) d'appel :</h3>
@if (!$queues || is_null($queues))
    Aucune file d'attente pour le moment.
@else
    @forelse ($queues as $index => $queue)
        <div class="mb-3 p-2 border rounded">
            Nom de la queue : <strong>{{ $queue['name'] }}</strong>
            <br>
            @if (!empty($queue['strategy']))
                Stratégie : {{ $queue['strategy'] }}
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
@endif
<br><br>

<h3>Heure(s) d'ouverture (H.O.)</h3>
@if (!$timetable_ho || is_null($timetable_ho))
    Pas d'horaires d'ouverture.
@else
    <pre
        style="white-space: pre-wrap; font-family: Arial, Helvetica, sans-serif; background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">{{ $timetable_ho }}</pre>
@endif
<br><br>

<h3>SVI (Serveur Vocal Interactif)</h3>
@if (!$svi || is_null($svi))
    Pas de SVI
@else
    <pre
        style="white-space: pre-wrap; font-family: Arial, Helvetica, sans-serif; background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">{{ $svi }}</pre>
@endif
<br><br>

<h3>Dialplan(s) :</h3>
@if (!$dialplan || is_null($dialplan))
    Pas de dialplan.
@else
    <pre
        style="white-space: pre-wrap; font-family: Arial, Helvetica, sans-serif; background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">{{ $dialplan }}</pre>
@endif
<br><br>

<h3>Information(s) supplémentaire(s) et remarque(s)</h3>
@if (!$infos_remarques || is_null($infos_remarques))
    Pas d'informations ou remarques supplémeentaires.
@else
    <pre
        style="white-space: pre-wrap; font-family: Arial, Helvetica, sans-serif; background-color: #f5f5f5; padding: 10px; border: 1px solid #ddd; border-radius: 4px;">{{ $infos_remarques }}</pre>
@endif
