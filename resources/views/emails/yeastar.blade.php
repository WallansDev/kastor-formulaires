@php
    // $customer_name = session('form_yeastar.customer_name');
    // $reseller_name = session('form_yeastar.reseller_name');
    // $reseller_email = session('form_yeastar.reseller_email');
    // $urlPbx = session('form_yeastar.url_pbx');
    // $portes = session('form_yeastar.numeros.portes', []);
    // $extensions = session('form_yeastar.extensions');
    // $callGroups = session('form_yeastar.callgroups');
    // $queues = session('form_yeastar.queues');
    // $timetable_ho = session('form_yeastar.timetable');
    // $svi = session('form_yeastar.svi');
    // $dialplan = session('form_yeastar.dialplan');
    // // $devices = session('form_yeastar.devices');
    // $infos_remarques = session('form_yeastar.infos_remarques');

    if (empty($urlPbx)) {
        $urlPbx = 'https://centrex.vokalise.fr';
    } else {
        $urlPbx = 'https://' . $urlPbx . '.vokalise.fr';
    }

@endphp


<h2 style="text-align:center; margin-bottom: 12px">Nouvelle commande PBX YEASTAR</h2>

<p>Émetteur : {{ $reseller_name }}</p>
<p>Email émetteur : {{ $reseller_email }}</p>
<p>Lien PBX : <a href="{{ $urlPbx }}">{{ $urlPbx }}</a></p>
