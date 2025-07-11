{{-- @php
    $customer_name = session('form.customer_name');
    $urlPbx = session('form.url_pbx');
    $portes = session('form.numeros.portes', []);
    $extensions = session('form.extensions');
    $callGroups = session('form.callgroups');
    $timetable_ho = session('form.timetable');
    $svi_options = session('form.svi_options');
    $dialplan = session('form.dialplan');
    $infos_remarques = session('form.infos_remarques');
@endphp --}}

<h3>Information(s) du client :</h3>
<strong style="font-size: 13px">Nom du client :</strong> {{ $customer_name }}
<br>
<strong style="font-size: 13px">Url du PBX :</strong> https://{{ $urlPbx }}.wildixin.com
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
            <th
                style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family: Arial, Helvetica, sans-serif;">
                Licence</th>

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
                <td
                    style="padding: 10px; border: 1px solid black; border-collapse: collapse; font-family:'Franklin Gotdic Medium', 'Arial Narrow', Arial, sans-serif;">
                    {{ ucfirst($extension['licence']) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<br><br>

<h3>Groupe(s) d'appel :</h3>
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
<br><br>

<h3>Heure(s) d'ouverture (H.O.)</h3>
@if (!$timetable_ho || is_null($timetable_ho))
Pas d'horaires d'ouverture.
@else
<textarea class="form-control" cols="100" rows="5" disabled>{{ $timetable_ho}}</textarea>
@endif
<br><br>

<h3>SVI :</h3>
@if (!$svi_options || is_null($timetable_ho))
    Pas de SVI.
@else
    @foreach ($svi_options as $option)
        Choix : {{ $option['ordre'] . ' = ' . $option['nom'] }}
        @if (!$loop->last)
            <br><br>
        @endif
    @endforeach
@endif
<br><br>

<h3>Dialplan(s) :</h3>
@if (!$dialplan || is_null($timetable_ho))
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