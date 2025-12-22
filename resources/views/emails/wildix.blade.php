@php
    $customer_name = session('form_wildix.customer_name');
    $reseller_name = session('form_wildix.reseller_name');
    $reseller_email = session('form_wildix.reseller_email');
    $urlPbx = session('form_wildix.url_pbx');
    $portes = session('form_wildix.numeros.portes', []);
    $extensions = session('form_wildix.extensions');
    $callGroups = session('form_wildix.callgroups');
    $queues = session('form_wildix.queues');
    $timetable_ho = session('form_wildix.timetable');
    $svi = session('form_wildix.svi');
    $dialplan = session('form_wildix.dialplan');
    $devices = session('form_wildix.devices');
    $infos_remarques = session('form_wildix.infos_remarques');

    $urlPbx = 'https://' . $urlPbx . '.wildixin.com';
@endphp


<h2 style="text-align:center; margin-bottom: 12px">Nouvelle commande PBX WILDIX</h2>

<p>Émetteur : {{ $reseller_name }}</p>
<p>Email émetteur : {{ $reseller_email }}</p>
<p>Lien PBX : <a href="{{ $urlPbx }}">{{ $urlPbx }}</a></p>
