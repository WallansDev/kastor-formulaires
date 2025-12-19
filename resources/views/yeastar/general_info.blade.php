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

        <form method="POST" action="{{ route('yeastar.general_info') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-5">

                    <h4>Information(s) générale(s)</h4>
                    <label for="reseller_name" class="form-label">Nom du revendeur <span class="required-star">
                            *</span></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="reseller_name" name="reseller_name"
                            value="{{ old('reseller_name', $data['reseller_name'] ?? '') }}"
                            placeholder="Raison sociale - Nom" required>
                    </div>
                    <label for="reseller_email" class="form-label">Email destinataire du récapitulatif <span
                            class="required-star">
                            *</span></label>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" id="reseller_email" name="reseller_email"
                            value="{{ old('reseller_email', $data['reseller_email'] ?? '') }}"
                            placeholder="john.doe@exemple.com" required>
                    </div>
                    <br><br>

                    <h4>Information(s) IPBX</h4>
                    <label for="customer_name" class="form-label">Nom du client final<span class="required-star">
                            *</span></label>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                            value="{{ old('customer_name', $data['customer_name'] ?? '') }}" required>
                    </div>

                    <label for="url_pbx" class="form-label">URL de l'IPBX</label>
                    <div class="input-group mb-3">
                        <span class="input-group-text">https://</span>
                        <input type="text" class="form-control" id="url_pbx" name="url_pbx"
                            value="{{ old('url_pbx', $data['url_pbx'] ?? '') }}" placeholder="Entrer nom">
                        <span class="input-group-text">.vokalise.fr</span>
                    </div>
                </div>
            </div>
            <a href="{{ route('home') }}" style="float:left;" class="btn btn-secondary mt-5">Précédent</a>
            <button type="submit" style="float:right;" class="btn btn-success mt-5">Suivant</button>
        </form>
    </div>
@endsection
