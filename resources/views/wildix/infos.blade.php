@extends('layouts.base')

@section('title', 'Infos et remarques')

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

        <form method="POST" action="{{ route('wildix.infos') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-12">
                    <label for="infos_remarques" class="form-label">Informations et/ou remarques supplémentaires ?</label>
                    <div class="input-group mb-3">
                        <textarea class="form-control" name="infos_remarques" id="infos_remarques" cols="600" rows="5">{{ old('form_wildix.infos_remarques', $data['infos_remarques'] ?? '') }}</textarea>
                    </div>
                </div>
            </div>
            <button type="submit" name="previous" value="1" style="float:left;"
                class="btn btn-secondary mt-5">Précédent</button>
            <button type="submit" style="float:right;" class="btn btn-success mt-5 mb-5">Suivant</button>
        </form>
    </div>
@endsection
