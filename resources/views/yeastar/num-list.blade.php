@extends('layouts.base')

@section('title', 'Informations IPBX')

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

        <form action="{{ route('yeastar.num_list') }}" method="POST">
            @csrf
            <div class="row mb-5">
                <div class="col-6">
                    <div class="mb-4 mt-4">
                        {{-- Label sur sa propre ligne --}}
                        <label for="numero_porte" class="form-label">
                            Ajouter un numéro porté/créé <span class="required-star">*</span>
                        </label>

                        {{-- Ligne avec input et bouton plus petits --}}
                        <div class="row g-2 align-items-center">
                            <div class="col-6">
                                <input type="text" name="numero_porte" class="form-control" placeholder="+33XXXXXXXXX"
                                    value="{{ old('numero_porte') }}">
                            </div>
                            <div class="col-1">
                                <button class="btn-add2" type="submit" name="action_type" value="ajouter_porte">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Message d'erreur --}}
                        @error('numero_porte')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- Ajouter un numéro provisoire --}}
                    <label for="numero_provisoire" class="form-label">Ajouter un numéro provisoire</label>
                    <div class="mb-3">
                        @error('numero_provisoire')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        @error('porte_selectionne')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <div class="row g-2 align-items-center">
                            <div class="col-4">
                                <input type="text" name="numero_provisoire" class="form-control"
                                    placeholder="+33XXXXXXXXX" value="{{ old('numero_provisoire') }}">
                            </div>
                            <div class="col-6">
                                <select class="form-control" name="porte_selectionne">
                                    <option value="" selected disabled>-- Sélectionner le numéro porté/créé --
                                    </option>
                                    @foreach ($data['portes'] as $porte)
                                        @if (!$porte['provisoire'])
                                            <option value="{{ $porte['numero'] }}"
                                                {{ old('porte_selectionne') == $porte['numero'] ? 'selected' : '' }}>
                                                {{ $porte['numero'] }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-1">
                                <button class="btn-add2" type="submit" name="action_type" value="ajouter_provisoire">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="col-6 mt-4 num_list">
                    <h4>Liste des numéros portés/créés et provisoires :</h4>
                    <ul>
                        @foreach ($data['portes'] as $index => $porte)
                            <li>
                                Porté : {{ $porte['numero'] }}
                                <button class="delete-button" type="submit" name="action_type"
                                    value="supprimer_porte_{{ $index }}"><i class="fa fa-trash" aria-hidden="true"
                                        style="color: darkred;"></i></button>
                                @if ($porte['provisoire'])
                                    ➔ Provisoire : {{ $porte['provisoire'] }}
                                    <button class="delete-button" type="submit" name="action_type"
                                        value="supprimer_provisoire_{{ $index }}"><i class="fa fa-trash"
                                            aria-hidden="true" style="color: darkred;"></i></button>
                                @else
                                    ➔ Pas de provisoire
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </form>
        <a href="{{ route('yeastar.general_info') }}" style="float:left;" class="btn btn-secondary mt-5">Précédent</a>
        <a href="{{ route('yeastar.extension') }}" style="float:right;" class="btn btn-success mt-5 mb-5">Suivant</a>
    @endsection
