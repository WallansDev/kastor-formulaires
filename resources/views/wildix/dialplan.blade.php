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
            @include('layouts.header')
        </div>

        <form method="POST" action="{{ route('wildix.dialplan') }}">
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
                    @if (!session('form_wildix.callgroups'))
                        Pas de groupe d'appel
                    @else
                        @foreach ($data['callgroups'] as $callgroup)
                            <ul>
                                <li style="list-style: square;">
                                    {{ $callgroup['name'] . ' - ' . $callgroup['type'] }}
                                    <ul>
                                        @foreach ($callgroup['ext'] as $extension)
                                            <li style="list-style: circle;">{{ $extension }}</li>
                                        @endforeach
                                    </ul>
                                </li>
                            </ul>
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-4">
                    <div class="text-center">
                        <h6>SVI disponible</h6>
                    </div>
                    @if (!session('form_wildix.svi'))
                        Pas de SVI
                    @else
                        {{ $data['svi'] }}
                    @endif
                </div>
            </div>
            <button type="submit" name="previous" value="1" style="float:left;"
                class="btn btn-secondary mt-5">Précédent</button>
            <button type="submit" style="float:right;" class="btn btn-success mt-5 mb-5">Suivant</button>
        </form>
    </div>
@endsection
