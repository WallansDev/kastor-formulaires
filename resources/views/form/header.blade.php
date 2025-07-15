@php
    $currentRoute = Route::currentRouteName();
    $steps = [
        ['route' => 'form.pbx-info', 'label' => 'Informations IPBX', 'required' => 'form.url_pbx'],
        ['route' => 'form.num-list', 'label' => 'Numéros de téléphone', 'required' => 'form.numeros.portes'],
        ['route' => 'form.extension', 'label' => 'Extensions', 'required' => 'form.extensions'],
        ['route' => 'form.call-group', 'label' => 'Call Groups', 'required' => null],
        ['route' => 'form.timetable', 'label' => "Heures d'ouverture", 'required' => 'form.timetable_ho'],
        ['route' => 'form.svi', 'label' => 'SVI', 'required' => null],
        ['route' => 'form.dialplan', 'label' => 'Dialplan', 'required' => 'form.dialplan'],
        ['route' => 'form.infos', 'label' => 'Informations et remarques', 'required' => null],
        ['route' => 'form.recap', 'label' => 'Récapitulatif', 'required' => 'form.recap'],
    ];

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
            // Si une étape requise n'est pas remplie, on bloque toutes les suivantes
            if ($step['required'] && !session($step['required'])) {
                $canDisplay = false;
            }
        @endphp
    @endforeach
</p>
