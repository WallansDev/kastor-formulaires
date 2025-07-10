@extends('layouts.base')

@section('title', 'Configuration du dialplan')

@section('content')
    <div class="container">

        {{-- DEBUG --}}
        <a href="{{ route('debug.session') }}">Dump Session</a>

        <div class="row">
            <form action="{{ route('form.reset') }}" method="POST">
                @csrf
                <button type="submit" style="float:right;" class="btn btn-outline-danger"><i class="fa fa-trash"
                        aria-hidden="true" style="color: darkred;"></i> Vider la session</button>
            </form>

            <div class="text-center col-12 mt-3">
                @include('form.header')
            </div>
        </div>

        <form method="POST" action="{{ route('form.dialplan') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-12">
                    <label for="dialplan" class="form-label">Dialplan <span class="required-star">
                            *</span></label>
                    <div class="input-group mb-1">
                        <small><i>NB : Précisez si le répondeur et avec ou sans messagerie.</i></small>
                    </div>
                    <textarea class="form-control" name="dialplan" id="dialplan" cols="600" rows="5"
                        placeholder="Ex : +339XXXXXXXX : 201 - Accueil (20 s.) → CG_ALL (Groupe d'appel) (20 s.) → REPONDEUR">{{ old('dialplan', $data['dialplan'] ?? '') }}</textarea>
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
                </div>
            </div>
            <button type="submit" style="float:right;" class="btn btn-success mt-5">Suivant</button>
        </form>
    </div>
@endsection
