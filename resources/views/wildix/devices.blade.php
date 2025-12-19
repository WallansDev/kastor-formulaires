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
            <form action="{{ route('wildix.reset') }}" method="POST">
                @csrf
                <br>
                <button type="submit" style="float:right;" class="btn btn-outline-danger"><i class="fa fa-trash"
                        aria-hidden="true" style="color: darkred;"></i> Vider la session</button>
            </form>

            <div class=" text-center col-12 mt-3">
                @include('layouts.header')
            </div>
        </div>

        <form method="POST" action="{{ route('wildix.device') }}">
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
                    <div class="row align-items-center">
                        {{-- WIRED PHONES --}}
                        <div class="col-md-5">
                            <label for="device_name" class="form-label">{{ mb_strtoupper('Téléphones filaires :') }}</label>
                            <select class="form-control" name="device_name" id="device_name">
                                <option value="" disabled hidden selected>-- Choisir un équipement --</option>
                                <option value="" disabled>-- FILAIRES --</option>
                                <option value="SuperVision 5">SuperVision 5</option>
                                <option value="WelcomeConsole 5">WelcomeConsole 5</option>
                                <option value="Start 5">Start 5</option>
                                <option value="WorkForce 5">WorkForce 5</option>
                                <option value="ForcePro 5">ForcePro 5</option>
                                <option value="Vision">Vision</option>
                            </select>
                        </div>

                        {{-- EXTENSION --}}
                        <div class="col-md-6">
                            <label for="extension" class="form-label">Associer une extension :</label>
                            <div class="input-group">
                                <select class="form-control" name="extension" id="extension">
                                    <option value="" disabled selected>-- Choisir une extension --</option>
                                    @foreach ($data['extensions'] as $ext)
                                        <option value="{{ $ext['extension'] }}">
                                            {{ $ext['extension'] . ' - ' . $ext['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-success ms-2" style="padding:10px; height:38px !important;"
                                    name="action_type" value="add_device">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row align-items-center mt-4">
                        {{-- WIRELESS PHONE --}}
                        <div class="col-md-5">
                            <label for="device_name"
                                class="form-label">{{ mb_strtoupper('Téléphones sans-fils :') }}</label>
                            <select class="form-control" name="device_name" id="device_name">
                                <option value="" disabled hidden selected>-- Choisir un équipement --</option>
                                <option value="" disabled>-- DECT --</option>
                                <option value="W-AIR Basic2">W-AIR Basic2</option>
                                <option value="W-AIR Office">W-AIR Office</option>
                                <option value="W-AIR LifeSaver">W-AIR LifeSaver</option>
                                <option value="W-AIR LifeSaver-EX">W-AIR LifeSaver-EX</option>
                                <option value="W-AIR Med">W-AIR Med</option>
                                <option value="" disabled>-- Wi-Fi --</option>
                                <option value="Wi-One">Wi-One</option>
                            </select>
                        </div>

                        {{-- EXTENSION --}}
                        <div class="col-md-6">
                            <label for="extension" class="form-label">Associer une extension :</label>
                            <div class="input-group">
                                <select class="form-control" name="extension" id="extension">
                                    <option value="" disabled selected>-- Choisir une extension --</option>
                                    @foreach ($data['extensions'] as $ext)
                                        <option value="{{ $ext['extension'] }}">
                                            {{ $ext['extension'] . ' - ' . $ext['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-success ms-2" style="padding:10px; height:38px !important;"
                                    name="action_type" value="add_device">
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
                        <label for="sip_gateway" class="form-label">{{ mb_strtoupper('Paserelles SIP :') }}</label>
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




                    <div class="row align-items-center mt-4">
                        {{-- WIRELESS PHONE --}}
                        <div class="col-md-5">
                            <label for="headset" class="form-label">{{ mb_strtoupper('Casques :') }}</label>
                            <select class="form-control" name="device_name" id="device_name">
                                <option value="" disabled hidden selected>-- Choisir un équipement --</option>
                                <option value="" disabled>-- FILAIRES --</option>
                                <option value="MonoLED">MonoLED</option>
                                <option value="DuoLED">DuoLED</option>
                                <option value="" disabled>-- BLUETOOTH --</option>
                                <option value="MonoLED BT">MonoLED BT</option>
                                <option value="DuoLED BT">DuoLED BT</option>
                            </select>
                        </div>

                        {{-- EXTENSION --}}
                        <div class="col-md-6">
                            <label for="extension" class="form-label">Associer une extension :</label>
                            <div class="input-group">
                                <select class="form-control" name="extension" id="extension">
                                    <option value="" disabled selected>-- Choisir une extension --</option>
                                    @foreach ($data['extensions'] as $ext)
                                        <option value="{{ $ext['extension'] }}">
                                            {{ $ext['extension'] . ' - ' . $ext['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-success ms-2" style="padding:10px; height:38px !important;"
                                    name="action_type" value="add_device">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <br>
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

            <button type="submit" name="previous" value="1" style="float:left;" class="btn btn-secondary mt-5">Précédent</button>
            <button type="submit" style="float:right;" class="btn btn-success mt-5 mb-5">Suivant</button>
        </form>
    </div>
@endsection
