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

        <form method="POST" action="{{ route('form.infos') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-12">
                    <label for="infos_remarques" class="form-label">Informations et/ou remarques supplémentaires ?</label>
                    <div class="input-group mb-3">
                        <textarea class="form-control" name="infos_remarques" id="infos_remarques" cols="600" rows="5">{{ old('infos_remarques', $data['infos_remarques'] ?? ' ') }}</textarea>
                    </div>
                </div>
            </div>
            <button type="submit" style="float:right;" class="btn btn-success">Suivant</button>
        </form>
    </div>
@endsection
