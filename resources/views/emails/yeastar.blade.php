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
    Pas de groupe d'appel
@else
    @foreach ($callGroups as $index => $callgroup)
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
        @if (!$loop->last)
            <br><br>
        @endif
    @endforeach
@endif
<br><br>

<h3>Queue(s) d'appel :</h3>
@if (!$queues || is_null($queues))
    Pas de queue d'appel
@else
    @foreach ($queues as $index => $queue)
        Nom de la queue : {{ $queue['name'] }}
        <br>
        Extensions associées :
        @foreach ($queue['ext'] as $queue)
            {{ $extension }}
            @if (!$loop->last)
                +
            @endif
        @endforeach
        @if (!$loop->last)
            <br><br>
        @endif
    @endforeach
@endif
<br><br>

<h3>Heure(s) d'ouverture (H.O.)</h3>
@if (!$timetable_ho || is_null($timetable_ho))
    Pas d'horaires d'ouverture.
@else
    <textarea class="form-control" cols="100" rows="5" disabled>{{ $timetable_ho }}</textarea>
@endif
<br><br>

<h3>SVI (Serveur Vocal Interactif)</h3>
@if (!$svi || is_null($svi))
    Pas de SVI
@else
    <textarea class="form-control" cols="100" rows="5" disabled>{{ $svi }}</textarea>
@endif
<br><br>

<h3>Dialplan(s) :</h3>
@if (!$dialplan || is_null($dialplan))
    Pas de dialplan.
@else
    <textarea class="form-control" cols="100" rows="5" disabled>{{ $dialplan }}</textarea>
@endif
<br><br>

<h3>Information(s) supplémentaire(s) et remarque(s)</h3>
@if (!$infos_remarques || is_null($infos_remarques))
    Pas d'informations ou remarques supplémeentaires.
@else
    <textarea class="form-control" cols="100" rows="5" disabled>{{ $infos_remarques }}</textarea>
@endif
