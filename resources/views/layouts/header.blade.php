@php
    $currentRoute = Route::currentRouteName();
    $prefix = explode('.', $currentRoute)[0]; // Récupère la partie avant le point (wildix ou yeastar)

    // Étapes de base
    $steps_wildix = [
        ['route' => 'wildix.general_info', 'label' => 'Informations IPBX', 'required' => 'form_wildix.url_pbx'],
        ['route' => 'wildix.num_list', 'label' => 'Numéros de téléphone', 'required' => 'form_wildix.numeros.portes'],
        ['route' => 'wildix.extension', 'label' => 'Extensions', 'required' => 'form_wildix.extensions'],
        ['route' => 'wildix.device', 'label' => 'Équipements', 'required' => 'form_wildix.devices'],
        ['route' => 'wildix.call_group', 'label' => 'Call Groups', 'required' => null],
        ['route' => 'wildix.timetable', 'label' => "Heures d'ouverture", 'required' => null],
        ['route' => 'wildix.svi', 'label' => 'SVI', 'required' => null],
        ['route' => 'wildix.dialplan', 'label' => 'Dialplan', 'required' => 'form.dialplan'],
        ['route' => 'wildix.infos', 'label' => 'Informations et remarques', 'required' => null],
        ['route' => 'wildix.recap', 'label' => 'Récapitulatif', 'required' => 'form.recap'],
    ];

    $steps_yeastar = [
        ['route' => 'yeastar.general_info', 'label' => 'Informations IPBX', 'required' => 'form_yeastar.url_pbx'],
        ['route' => 'yeastar.num_list', 'label' => 'Numéros de téléphone', 'required' => 'form_yeastar.numeros.portes'],
        ['route' => 'yeastar.extension', 'label' => 'Extensions', 'required' => 'form_yeastar.extensions'],
        // ['route' => 'yeastar.device', 'label' => 'Équipements', 'required' => 'form_yeastar.devices'],
        ['route' => 'yeastar.call_group', 'label' => 'Call Groups', 'required' => null],
        ['route' => 'yeastar.timetable', 'label' => "Heures d'ouverture", 'required' => null],
        ['route' => 'yeastar.svi', 'label' => 'SVI', 'required' => null],
        ['route' => 'yeastar.dialplan', 'label' => 'Dialplan', 'required' => 'form.dialplan'],
        ['route' => 'yeastar.infos', 'label' => 'Informations et remarques', 'required' => null],
        ['route' => 'yeastar.recap', 'label' => 'Récapitulatif', 'required' => 'form.recap'],
    ];
@endphp

<div class="mt-5 mb-4" style="display:flex; align-items: center; justify-content: space-between;">
    @if ($prefix === 'wildix')
        <img src="{{ asset('images/wildix.png') }}" alt="Wildix Logo" width="5%" style="float:left">
    @elseif ($prefix === 'yeastar')
        <img src="{{ asset('images/yeastar.png') }}" alt="Yeastar Logo" width="5%" style="float:left">
    @else
        <p>Logo non trouvé</p>
    @endif

    <form action="{{ route('wildix.reset') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger"><i class="fa fa-trash" aria-hidden="true"
                style="color: darkred;"></i> Vider
            la session</button>
    </form>
</div>

<div class=" text-center col-12 mb-4">
    <a href="{{ route('home') }}">Accueil</a>
    @if ($prefix === 'wildix')
        @foreach ($steps_wildix as $step)
            >
            @if ($currentRoute === $step['route'])
                <strong><a href="{{ route($step['route']) }}">{{ $step['label'] }}</a></strong>
            @else
                <a href="{{ route($step['route']) }}">{{ $step['label'] }}</a>
            @endif

            @php
                if ($step['required'] && !session($step['required'])) {
                    $canDisplay = false;
                }
            @endphp
        @endforeach
    @else
        @foreach ($steps_yeastar as $step)
            >
            @if ($currentRoute === $step['route'])
                <strong><a href="{{ route($step['route']) }}">{{ $step['label'] }}</a></strong>
            @else
                <a href="{{ route($step['route']) }}">{{ $step['label'] }}</a>
            @endif

            @php
                if ($step['required'] && !session($step['required'])) {
                    $canDisplay = false;
                }
            @endphp
        @endforeach
    @endif
</div>
