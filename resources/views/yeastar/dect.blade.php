@extends('layouts.base')

@section('title', 'DECT')

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

        <form method="POST" action="{{ route('form.device') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-6">
                    <h4>Ajouter un équipement :</h4>
                    <br>
                    {{-- PHONES DECT --}}
                    <div class="col-9">
                        <label for="reseller_name" class="form-label">{{ mb_strtoupper('Téléphones dect :') }}</label>
                        <div class="input-group mb-3">
                            <select class="form-control" name="device_name" id="device_name">
                                <option value="" disabled hidden selected>-- Choisir un équipement --</option>
                                <option value="" disabled>-- DECT --</option>
                                <option value="W-AIR Basic2">W-AIR Basic2</option>
                                <option value="W-AIR Office">W-AIR Office</option>
                                <option value="W-AIR LifeSaver">W-AIR LifeSaver</option>
                                <option value="W-AIR LifeSaver-EX">W-AIR LifeSaver-EX</option>
                                <option value="W-AIR Med">W-AIR Med</option>
                            </select>
                            &ensp;
                            <div class="col-1">
                                <button class="btn btn-sm btn-success float-end" style="padding:10px" name="action_type"
                                    value="add_device">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <label for="extension" class="form-label">Associer une extension :</label>
                        <div class="input-group mb-3">
                            <select class="form-control" name="extension" id="extension">
                                <option value="" disabled selected>-- Choisir une extension --</option>
                                @foreach ($data['extensions'] as $index => $ext)
                                    <option value="{{ $ext['extension'] }}">
                                        {{ $ext['extension'] . ' - ' . $ext['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <h4>Équipements :</h4>
                    <br>
                    @if (empty($data['devices']))
                        <p>Aucun équipement pour le moment.</p>
                    @else
                        @foreach ($data['devices'] as $index => $device)
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
                                @endif
                            </div>
                        @endforeach
                    @endif

                </div>

            </div>

            <div class="row mt-5 mb-3">
                <div class="col-10"></div>
                <div class="col-2">
                    {{-- <button type="submit" style="float:left;" class="btn btn-secondary mt-5">Précédent</button> --}}
                    <a href="{{ route('form.call-group') }}" style="float:right;" class="btn btn-success">Suivant</a>
                </div>
            </div>
        </form>
    </div>
@endsection
