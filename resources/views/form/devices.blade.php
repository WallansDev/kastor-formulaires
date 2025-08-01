@extends('layouts.base')

@section('title', 'Équipements Wildix')

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

        <form method="POST" action="{{ route('form.device') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-6">
                    <h4>Ajouter un équipement :</h4>
                    <br>
                    <div class="col-9">
                        <label for="reseller_name" class="form-label">Équipements Wildix <span class="required-star">
                                *</span></label>
                        <div class="input-group mb-3">
                            <select class="form-control" name="device_name" id="device_name">
                                <option value="" disabled selected>-- Choisir un équipement --</option>
                                <option value="" disabled>-- BASE STATIONS --</option>
                                <option value="W‑AIR Small Business">W‑AIR Small Business</option>
                                <option value="W-AIR Sync Plus Base">W-AIR Sync Plus Base</option>
                                <option value="" disabled>-- HANDSETS --</option>
                                <option value="W-AIR Basic2">W-AIR Basic2</option>
                            </select>
                        </div>
                        <label for="extension" class="form-label">Extensions :</label>
                        <div class="input-group mb-3">
                            <select class="form-control" name="extension" id="extension">
                                <option value="" disabled selected>-- Choisir une extension --</option>
                                @foreach ($data['extensions'] as $index => $ext)
                                    <option value="{{ $ext['extension'] }}">{{ $ext['extension'] . ' - ' . $ext['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-10">
                        <button class="btn btn-sm btn-success float-end" name="action_type" value="add_device">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="col-6">
                    <h4>Équipements :</h4>
                    <br>
                    @forelse ($data['devices'] as $index => $device)
                        <div class="mb-3 p-2 border rounded">
                            <strong>{{ $device['device_name'] }}</strong>
                            <button class="btn btn-sm btn-danger float-end" name="action_type"
                                value="delete_device_{{ $index }}">
                                <i class="fa fa-trash"></i>
                            </button>

                            @if (!empty($device['extension']))
                                <ul class="mt-2">
                                    <li>
                                        {{ $device['extension'] }}
                                    </li>
                                </ul>
                            @else
                                <p class="text-muted">Aucune extension assignée.</p>
                            @endif
                        </div>
                    @empty
                        <p>Aucun équipement pour le moment.</p>
                    @endforelse
                </div>

            </div>

            <div class="row mt-5">
                <div class="col-10"></div>
                <div class="col-2">
                    <a href="{{route('form.call-group')}}" style="float:right;" class="btn btn-success">Suivant</a>
                </div>
            </div>
        </form>
    </div>
@endsection
