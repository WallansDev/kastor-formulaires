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
                    {{-- BASES --}}
                    <div class="col-9">
                        <label for="reseller_name" class="form-label">{{ mb_strtoupper('Bases :') }}</label>
                        <div class="input-group mb-3">
                            <select class="form-control" name="device_name" id="device_name">
                                <option value="" disabled hidden selected>-- Choisir un équipement --</option>
                                <option value="" disabled>-- BASES MULTI-CELL --</option>
                                <option value="W-AIR SYNC PLUS BASE">W-AIR SYNC PLUS BASE</option>
                                <option value="W-AIR SYNC PLUS BASE OUTDOOR">W-AIR SYNC PLUS BASE OUTDOOR</option>
                                <option value="" disabled>-- BASES MONO-CELL --</option>
                                <option value="W-AIR SMALL BUSINESS">W-AIR SMALL BUSINESS</option>
                                <option value="" disabled>-- {{ mb_strtoupper('répéteurs') }} --</option>
                                <option value="W-AIR REPEATER">W-AIR REPEATER</option>
                            </select>
                            &ensp;
                            <div class="col-1">
                                <button class="btn btn-sm btn-success float-end" style="padding:10px" name="action_type"
                                    value="add_device">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                     <br>
                    <hr class="col-10">
                    <br>
                    {{-- SIP GATEWAY --}}
                    <div class="col-9">
                        <label for="reseller_name" class="form-label">{{ mb_strtoupper('Paserelles SIP :') }}</label>
                        <div class="input-group mb-3">
                            <select class="form-control" name="device_name" id="device_name">
                                <option value="" disabled hidden selected>-- Choisir un équipement --</option>
                                <option value="" disabled>-- FXS --</option>
                                <option value="W02FXS">W02FXS</option>
                                <option value="W24FXS">W24FXS</option>

                                <option value="" disabled>-- FXO --</option>
                                <option value="W04FXO">W04FXO</option>

                                <option value="" disabled>-- GSM-LTE --</option>
                                <option value="DaySaver">DaySaver</option>

                                <option value="" disabled>-- SIP W-PA --</option>
                                <option value="SIP W-PA">SIP W-PA</option>
                            </select>
                            &ensp;
                            <div class="col-1">
                                <button class="btn btn-sm btn-success float-end" style="padding:10px" name="action_type"
                                    value="add_device">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <br>
                    <hr class="col-10">
                    <br>
                    {{-- HEADSETS --}}
                    <div class="col-9">
                        <label for="reseller_name" class="form-label">{{ mb_strtoupper('Casques :') }}</label>
                        <div class="input-group">
                            <select class="form-control" name="device_name" id="device_name">
                                <option value="" disabled hidden selected>-- Choisir un équipement --</option>
                                <option value="" disabled>-- FILAIRES --</option>
                                <option value="MonoLED">MonoLED</option>
                                <option value="DuoLED">DuoLED</option>
                                <option value="" disabled>-- BLUETOOTH --</option>
                                <option value="MonoLED BT">MonoLED BT</option>
                                <option value="DuoLED BT">DuoLED BT</option>
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
                                    <option value="{{ $ext['extension'] }}">{{ $ext['extension'] . ' - ' . $ext['name'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
 <br>
                    <hr class="col-10">
                    <br>
                    {{-- PHONES --}}
                    <div class="col-9">
                        <label for="reseller_name" class="form-label">{{ mb_strtoupper('Téléphones :') }}</label>
                        <div class="input-group mb-3">
                            <select class="form-control" name="device_name" id="device_name">
                                <option value="" disabled hidden selected>-- Choisir un équipement --</option>
                                <option value="" disabled>-- FILAIRES --</option>
                                <option value="SuperVision 5">SuperVision 5</option>
                                <option value="WelcomeConsole 5">WelcomeConsole 5</option>
                                <option value="Start 5">Start 5</option>
                                <option value="WorkForce 5">WorkForce 5</option>
                                <option value="ForcePro 5">ForcePro 5</option>
                                <option value="Vision">Vision</option>
                                <option value="" disabled>-- Wi-Fi --</option>
                                <option value="Wi-One">Wi-One</option>
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

            <div class="row mt-5">
                <div class="col-10"></div>
                <div class="col-2">
                    <button type="submit" style="float:right;" class="btn btn-success">Suivant</button>
                </div>
            </div>
        </form>
    </div>
@endsection
