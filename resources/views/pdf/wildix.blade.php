<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Dossier de paramétrage</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        #main-title {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th,
        td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .section-title {
            background-color: #ddd;
            padding: 6px;
            font-weight: bold;
        }
    </style>
</head>

<body>

    @php
        // $reseller_name = session('form_wildix.reseller_name');
        // $reseller_email = session('form_wildix.reseller_email');
        // $customer_name = session('form_wildix.customer_name');
        // $urlPbx = session('form_wildix.url_pbx');
        // $portes = session('form_wildix.numeros.portes', []);
        // $extensions = session('form_wildix.extensions');
        // $callGroups = session('form_wildix.callgroups');
        // $queues = session('form_wildix.queues');
        // $timetable_ho = session('form_wildix.timetable');
        // $svi = session('form_wildix.svi');
        // $dialplan = session('form_wildix.dialplan');
        // $devices = session('form_wildix.devices');
        // $infos_remarques = session('form_wildix.infos_remarques');
        $date = date('d/m/Y à H\hi');
    @endphp

    <div id="main-title">
        <img src="{{ public_path('images/kastor.png') }}" width="10%" style="float: left;" alt="logo_kastor.png">
        <h1>Informations de paramétrages IPBX - {{ $customer_name }}</h1>
        <h3>Le {{ $date }}</h3>
    </div>

    <br><br>
    <hr>
    <h3>Information(s) générale(s) :</h3>
    <strong style="font-size: 13px">Nom du revendeur :</strong> {{ $reseller_name }}
    <br>
    <strong style="font-size: 13px">Email du revendeur :</strong> {{ $reseller_email }}
    <br><br>
    <hr>
    <h3>Information(s) du client :</h3>
    <strong style="font-size: 13px">Nom du client :</strong> {{ $customer_name }}
    <br>
    <strong style="font-size: 13px">Url du PBX :</strong> https://{{ $urlPbx }}.wildixin.com
    <br><br>
    <hr>

    <h3>Numéro(s) porté(s)/créé(s) & provisoire(s) :</h3>
    @foreach ($portes as $porte)
        <strong>Porté / Créé :</strong> {{ $porte['numero'] }} =>
        <strong>Provisoire :</strong> {{ $porte['provisoire'] ?? 'Aucun' }}
        @if (!$loop->last)
            <br>
        @endif
    @endforeach
    <br><br>
    <hr>
    <h3>Extensions :</h3>
    <table>
        <thead>
            <tr>
                <th>Extension</th>
                <th>Nom affiché</th>
                <th>Email</th>
                <th>Office</th>
                <th>Langue</th>
                <th>Licence</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($extensions as $ext)
                <tr>
                    <td>{{ $ext['extension'] }}</td>
                    <td>{{ $ext['name'] }}</td>
                    <td>{{ $ext['email'] ?? '—' }}</td>
                    <td>{{ $ext['numPorte'] ?? '—' }}</td>
                    <td>{{ $ext['language'] ?? '—' }}</td>
                    <td>{{ $ext['licence'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Aucune extension enregistrée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <hr>
    <h3>Équipements : </h3>
    <table>
        <thead>
            <tr>
                <th>Nom de l'équipement</th>
                <th>Extension liée</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($devices as $device)
                <tr>
                    <td>{{ $device['device_name'] }}</td>
                    <td>{{ $device['extension'] ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Aucun équipement défini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <hr>
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
    <hr>
    <h3>Queue(s) d'appel :</h3>
    @if (!$queues || is_null($queues))
        Pas de queue d'appel
    @else
        @foreach ($queues as $index => $queue)
            Nom de la queue : {{ $queue['name'] }}
            <br>
            Extensions associées :
            @foreach ($queue['ext'] as $extension)
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
    <hr>
    <h3>Heure(s) d'ouverture (H.O.)</h3>
    @if (!$timetable_ho || is_null($timetable_ho))
        Pas d'horaires d'ouverture.
    @else
        <textarea class="form-control" cols="100" rows="5" disabled>{{ $timetable_ho }}</textarea>
    @endif
    <br><br>
    <hr>

    <h3>SVI (Serveur Vocal Interactif)</h3>
    @if (!$svi || is_null($svi))
        Pas de SVI
    @else
        <textarea class="form-control" cols="100" rows="5" disabled>{{ $svi }}</textarea>
    @endif
    <br><br>
    <hr>

    <h3>Dialplan(s) :</h3>
    @if (!$dialplan || is_null($dialplan))
        Pas de dialplan.
    @else
        <textarea class="form-control" cols="100" rows="5" disabled>{{ $dialplan }}</textarea>
    @endif
    <br><br>
    <hr>

    <h3>Information(s) supplémentaire(s) et remarque(s)</h3>
    @if (!$infos_remarques || is_null($infos_remarques))
        Pas d'informations ou remarques supplémeentaires.
    @else
        <textarea class="form-control" cols="100" rows="5" disabled>{{ $infos_remarques }}</textarea>
    @endif
    <br><br>
    <br><br>
    <br><br>

</body>

</html>
