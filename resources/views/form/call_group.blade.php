@extends('layouts.base')

@section('title', 'Groupes d\'appels')

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
            <form action="{{ route('form.reset') }}" method="POST">
                @csrf
                <br>
                <button type="submit" style="float:right;" class="btn btn-outline-danger"><i class="fa fa-trash"
                        aria-hidden="true" style="color: darkred;"></i> Vider la session</button>
            </form>

            <div class=" text-center col-12 mt-3">
                @include('form.header')
            </div>
        </div>

        <form method="POST" action="{{ route('form.call-group') }}">
            @csrf
            <div class="row mt-5">
                <div class="col-6">
                    <h4>Créer un groupe d'appel</h4>
                </div>
                <div class="col-1"></div>
                <div class="col-5">
                    <h4>Groupes existants</h4>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    @error('cgName')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @error('cg_type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                    <input type="text" name="cgName" placeholder="Nom du groupe" class="form-control my-2"
                        value="{{ old('cgName') }}">
                    <select name="cg_type" class="form-control mb-3">
                        <option value="" disabled selected>-- Choisir un type --</option>
                        <option value="all_10">Appeler les 10 (all_10)</option>
                        <option value="linear">Linéaire (linear)</option>
                        <option value="round_robin">À tour de rôle (round_robin)</option>
                    </select>
                    <button class="btn btn-primary" style="float:right" name="action_type" value="add_group"><i
                            class="fa fa-plus"></i></button>

                </div>
                <div class="col-1"></div>
                <div class="col-5">
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

            <hr class="mt-4 col-6">

            <div class="row mt-5">
                <h4>Ajouter une extension à un groupe</h4>
            </div>

            <div class="row">
                <div class="col-6">
                    @error('cg_selectionne')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @error('ext_selectionne')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror

                    <div class="row mb-3">
                        <div class="col">
                            <select name="cg_selectionne" class="form-control">
                                <option value="" disabled selected>-- Choisir le groupe d'appel --</option>
                                @foreach ($callGroups as $group)
                                    <option value="{{ $group['name'] }}">{{ $group['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col">
                            <select name="ext_selectionne" class="form-control">
                                <option value="" disabled selected>-- Choisir l'extension --</option>
                                @foreach ($extensions as $ext)
                                    <option value="{{ $ext['extension'] }}">{{ $ext['extension'] }} - {{ $ext['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" name="action_type" value="add_ext">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 mb-3">
                <a href="{{ route('form.timetable') }}" style="float:right;" class="btn btn-success">Suivant</a>
            </div>

        </form>
    </div>
@endsection
