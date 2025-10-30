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

        <form method="POST" action="{{ route('form.pbx-info') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-5">

                    <h4>Information(s) générale(s)</h4>
                    <label for="reseller_name" class="form-label">Nom du revendeur <span class="required-star">
                            *</span></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="reseller_name" name="reseller_name"
                            value="{{ old('reseller_name', $data['reseller_name'] ?? '') }}" placeholder="Raison sociale + Nom" required>
                    </div>
                    <label for="reseller_email" class="form-label">Email destinataire du récapitulatif <span class="required-star">
                            *</span></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="reseller_email" name="reseller_email"
                            value="{{ old('reseller_email', $data['reseller_email'] ?? '') }}" placeholder="john.doe@exemple.com" required>
                    </div>
                    <br><br>

                    <h4>Information(s) IPBX</h4>
                    <label for="customer_name" class="form-label">Nom du client <span class="required-star">
                            *</span></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                            value="{{ old('customer_name', $data['customer_name'] ?? '') }}" required>
                    </div>

                    <label for="url_pbx" class="form-label">URL de l'IPBX <span class="required-star"> *</span></label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">https://</span>
                        <input type="text" class="form-control" id="url_pbx" name="url_pbx"
                            value="{{ old('url_pbx', $data['url_pbx'] ?? '') }}" placeholder="Entrer nom" required>
                        <span class="input-group-text">.wildixin.com</span>
                    </div>
                </div>
            </div>
            <button type="submit" style="float:right;" class="btn btn-success">Suivant</button>
        </form>
    </div>
@endsection
