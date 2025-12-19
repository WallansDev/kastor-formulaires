@extends('layouts.base')

@section('title', "Groupes d'appel")

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

        <form method="POST" action="{{ route('wildix.call_group') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-6">
                    <h4>Créer un groupe d'appel</h4>
                    @error('cgName')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @error('cg_type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <div class="row mb-3">
                        <div class="col">
                            <input type="text" name="cgName" placeholder="Nom du groupe" class="form-control"
                                value="{{ old('cgName') }}">
                        </div>
                        <div class="col">
                            <select name="cg_type" class="form-control">
                                <option value="" disabled selected>-- Choisir un type --</option>
                                <option value="all_10">Appeler les 10 (all_10)</option>
                                <option value="linear">Linéaire (linear)</option>
                                <option value="round_robin">À tour de rôle (round_robin)</option>
                            </select>
                        </div>

                        <div class="col">
                            <select name="ext_selectionne_new[]" class="form-control" multiple size="5">
                                <option value="" disabled>--- Liste d'extensions ---</option>
                                @foreach ($extensions as $ext)
                                    <option value="{{ $ext['extension'] }}">{{ $ext['extension'] }} -
                                        {{ $ext['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs
                                extensions</small>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" style="float:right" name="action_type" value="add_group"><i
                                    class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="row mb-3">

                    </div>
                </div>
                <div class="col"></div>
                <div class="col-5">
                    <h4>Associer des extension à un groupe d'appel</h4>
                    @error('cg_selectionne')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @error('ext_selectionne')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                    <div class="row mb-3">
                        <div class="col">
                            <select name="cg_selectionne" class="form-control">
                                <option value="" disabled selected>-- Choisir le groupe --</option>
                                @if (count($callGroups) > 0)
                                    <optgroup label="Groupes d'appel">
                                        @foreach ($callGroups as $group)
                                            <option value="{{ $group['name'] }}">{{ $group['name'] }}
                                                ({{ $group['type'] }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>

                        <div class="col">
                            <select name="ext_selectionne[]" class="form-control" multiple size="5">
                                <option value="" disabled>--- Liste d'extensions ---</option>
                                @foreach ($extensions as $ext)
                                    <option value="{{ $ext['extension'] }}">{{ $ext['extension'] }} -
                                        {{ $ext['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs
                                extensions</small>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" name="action_type" value="add_ext">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <hr>

            <div class="row mt-5">
                <div class="col-5">
                    <h4>Groupe d'appel existants :</h4>

                    @forelse ($callGroups as $index => $group)
                        <div class="mb-3 p-2 border rounded">
                            <strong>{{ $group['name'] }}</strong> (Type : {{ $group['type'] }})
                            <button class="btn btn-sm btn-danger float-end" name="action_type"
                                value="delete_group_{{ $index }}">
                                <i class="fa fa-trash"></i>
                            </button>

                            @if (!empty($group['ext']))
                                <ul class="mt-2">
                                    @foreach ($group['ext'] as $extIndex => $ext)
                                        <li>
                                            {{ $ext }}
                                            <button class="btn btn-sm btn-outline-danger" name="action_type"
                                                value="delete_ext|{{ $group['name'] }}|{{ $extIndex }}">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">Aucune extension assignée.</p>
                            @endif
                        </div>
                    @empty
                        <p>Aucun groupe d’appel pour le moment.</p>
                    @endforelse
                </div>
            </div>

            <div class="mt-5 mb-3">
                <button type="submit" name="previous" value="1" style="float:left;"
                    class="btn btn-secondary mt-5">Précédent</button>
                <a href="{{ route('wildix.timetable') }}" style="float:right;"
                    class="btn btn-success mt-5 mb-5">Suivant</a>
            </div>

        </form>
    </div>
@endsection
