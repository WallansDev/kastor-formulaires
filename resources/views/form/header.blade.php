@php
    $currentRoute = Route::currentRouteName();

    // Étapes de base
    $steps = [
        ['route' => 'form.pbx-info', 'label' => 'Informations IPBX', 'required' => 'form.url_pbx'],
        ['route' => 'form.num-list', 'label' => 'Numéros de téléphone', 'required' => 'form.numeros.portes'],
        ['route' => 'form.extension', 'label' => 'Extensions', 'required' => 'form.extensions'],
        ['route' => 'form.device', 'label' => 'Équipements', 'required' => 'form.devices'],
        ['route' => 'form.call-group', 'label' => 'Call Groups', 'required' => null],
        ['route' => 'form.timetable', 'label' => "Heures d'ouverture", 'required' => null],
        ['route' => 'form.svi', 'label' => 'SVI', 'required' => null],
        ['route' => 'form.dialplan', 'label' => 'Dialplan', 'required' => 'form.dialplan'],
        ['route' => 'form.infos', 'label' => 'Informations et remarques', 'required' => null],
        ['route' => 'form.recap', 'label' => 'Récapitulatif', 'required' => 'form.recap'],
    ];

    // Vérification d'un device spécial
$deviceNames = ['W-AIR SYNC PLUS BASE', 'W-AIR SYNC PLUS BASE OUTDOOR', 'W-AIR SMALL BUSINESS'];
$devices = session('form.devices', []);
$containsSpecialDevice = collect($devices)->contains(function ($device) use ($deviceNames) {
    return in_array($device['device_name'], $deviceNames);
});

// Si device spécial présent, on ajoute l'étape SVI à la bonne position (après timetable par exemple)
    if ($containsSpecialDevice) {
        array_splice($steps, 4, 0, [
            [
                'route' => 'form.dect',
                'label' => 'DECT',
                'required' => 'form.devices',
            ],
        ]);
    }

    $canDisplay = true;
@endphp

<p>
    <a href="{{ route('home') }}">Accueil</a>

    @foreach ($steps as $step)
        @if ($canDisplay)
            >
            @if ($currentRoute === $step['route'])
                <strong><a href="{{ route($step['route']) }}">{{ $step['label'] }}</a></strong>
            @else
                <a href="{{ route($step['route']) }}">{{ $step['label'] }}</a>
            @endif
        @endif

        @php
            // Si une étape requise n'est pas remplie, on bloque les suivantes
if ($step['required'] && !session($step['required'])) {
                $canDisplay = false;
            }
        @endphp
    @endforeach
</p>
