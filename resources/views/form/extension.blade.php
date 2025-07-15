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
                @error('extensions.unique')
                    <div class="text-danger">Numéro d'extension déjà existant.</div>
                @enderror
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

        <h4 class="mt-4">Liste des extensions :</h4>
        <form action="{{ route('form.extension') }}" method="POST">
            @csrf
            <button type="button" class="btn-add mb-2" onclick="ajouterExt()"><i class="fa fa-plus"
                    aria-hidden="true"></i></button>
            <button type="submit" class="btn-save mb-2"><i class="fa fa-save" aria-hidden="true"></i></button>
            <br>
            <small><i>NB : Sauvegarder les extensions avec le bouton disquette bleu avant de cliquer sur
                    continuer.</i></small>
            <br><br>
            <small><i><u>Numéros indisponibles :</u></i>
                <br>
                <ul>
                    <li>2 chiffres : 15, 17, 18.</li>
                    <li>3 chiffres : 100 à 199 (inclus).</li>
                    <li>4 chiffres : 3000 à 3999 (inclus) & 2222.</li>
                    <li>6 chiffres : 116000.</li>
                </ul>
            </small>
            <div class="row mt-3">
                <table class="table table-hover table-bordered table-striped">
                    <thead style="text-align: center">
                        <tr>
                            <th>#Extension</th>
                            <th>Nom affiché <span class="required-star">*</span></th>
                            <th>Email</th>
                            <th>#Présenté (appel sortant) <span class="required-star">*</span></th>
                            <th>Langue</th>
                            <th>Licence <span class="required-star">*</span></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="tableExt">
                        @php
                            $extensions = session('form.extensions', []);
                        @endphp
                        @foreach ($extensions as $index => $ext)
                            <tr>

                                <td><input type="number" name="extensions[{{ $index }}][extension]"
                                        value="{{ old('extensions.' . $index . '.extension', $ext['extension']) }}"
                                        class="form-control"></td>

                                <td><input type="text" name="extensions[{{ $index }}][name]"
                                        value="{{ old('extensions.' . $index . '.name', $ext['name']) }}"
                                        class="form-control">
                                </td>
                                <td><input type="email" name="extensions[{{ $index }}][email]"
                                        value="{{ old('extensions.' . $index . '.email', $ext['email'] ?? '') }}"
                                        class="form-control"></td>
                                <td>
                                    <select name="extensions[{{ $index }}][numPorte]" class="form-control" required>
                                        <option value="" disabled>-- Sélectionner le numéro porté --</option>
                                        @foreach ($data['portes'] as $porte)
                                            <option value="{{ $porte['numero'] }}"
                                                {{ old('extensions.' . $index . '.numPorte', $ext['numPorte'] ?? '') == $porte['numero'] ? 'selected' : '' }}>
                                                {{ $porte['numero'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <select name="extensions[{{ $index }}][language]" class="form-control" required>
                                        <option value="" disabled
                                            {{ old('extensions.' . $index . '.language', $ext['language'] ?? '') == '' ? 'selected' : '' }}>
                                            --- Choisir une langue ---</option>
                                        <option value="fr"
                                            {{ old('extensions.' . $index . '.language', $ext['language'] ?? '') == 'fr' ? 'selected' : '' }}>
                                            Français</option>
                                        <option value="en"
                                            {{ old('extensions.' . $index . '.language', $ext['language'] ?? '') == 'en' ? 'selected' : '' }}>
                                            Anglais</option>
                                        <option value="it"
                                            {{ old('extensions.' . $index . '.language', $ext['language'] ?? '') == 'it' ? 'selected' : '' }}>
                                            Italien</option>
                                        <option value="es"
                                            {{ old('extensions.' . $index . '.language', $ext['language'] ?? '') == 'es' ? 'selected' : '' }}>
                                            Espagnol</option>
                                    </select>
                                </td>

                                <td>
                                    <select name="extensions[{{ $index }}][licence]" class="form-control" required>
                                        <option value="" disabled
                                            {{ old('extensions.' . $index . '.licence', $ext['licence'] ?? '') == '' ? 'selected' : '' }}>
                                            --- Choisir une licence ---</option>
                                        <option value="premium"
                                            {{ old('extensions.' . $index . '.licence', $ext['licence'] ?? '') == 'premium' ? 'selected' : '' }}>
                                            Premium</option>
                                        <option value="business"
                                            {{ old('extensions.' . $index . '.licence', $ext['licence'] ?? '') == 'business' ? 'selected' : '' }}>
                                            Business</option>
                                        <option value="essential"
                                            {{ old('extensions.' . $index . '.licence', $ext['licence'] ?? '') == 'essential' ? 'selected' : '' }}>
                                            Essential</option>
                                        <option value="basic"
                                            {{ old('extensions.' . $index . '.licence', $ext['licence'] ?? '') == 'basic' ? 'selected' : '' }}>
                                            Basic</option>
                                        <option value="service"
                                            {{ old('extensions.' . $index . '.licence', $ext['licence'] ?? '') == 'service' ? 'selected' : '' }}>
                                            Service</option>
                                    </select>
                                </td>

                                <td style="text-align: center">
                                    <button type="submit" name="delete" value="{{ $index }}"
                                        class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"
                                            style="padding: 5px;"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
        <a href="{{ route('form.call-group') }}" style="float:right;" class="btn btn-success mt-5">Suivant</a>
    </div>
@endsection

@section('script-content')
    <script>
        function supprimerLigne(button) {
            const row = button.closest('tr');
            row.remove();
        }

        function ajouterExt() {
            let tableExt = document.getElementById('tableExt');
            let rowCount = tableExt.rows.length;
            let newRow = document.createElement('tr');

            newRow.innerHTML = `
            <td><input type="number" name="extensions[${rowCount}][extension]" min="1" class="form-control" type="number" value=""></td>

        <td><input type="text" name="extensions[${rowCount}][name]" class="form-control" value="" required></td>
        <td><input type="email" name="extensions[${rowCount}][email]" class="form-control" value=""></td>
        
        <td><select name="extensions[${rowCount}][numPorte]" class="form-control" required>
            <option value="" selected disabled>-- Sélectionner le numéro porté --</option>
            @foreach ($data['portes'] as $porte)
                <option value="{{ $porte['numero'] }}">
                    {{ $porte['numero'] }}
                </option>
            @endforeach
        </select></td>
        
        <td><select name="extensions[${rowCount}][language]" class="form-control" required>
            // <option value="" selected disabled>--- Choisir une langue ---</option>
            <option value="fr" selected>Français</option>
            <option value="en">Anglais</option>
            <option value="it">Italien</option>
            <option value="es">Espagnol</option>
        </select></td>

        <td><select name="extensions[${rowCount}][licence]" class="form-control" required>
            <option value="" selected disabled>--- Choisir une licence ---</option>
            <option value="premium">Premium</option>
            <option value="business">Business</option>
            <option value="essential">Essential</option>
            <option value="basic">Basic</option>
            <option value="service">Service</option>
        </select></td>

        <td style="text-align: center">
             <button type="button" onclick="supprimerLigne(this)" class="btn btn-danger btn-sm">
                <i class="fa-solid fa-trash" style="padding: 5px;"></i>
            </button>
        </td>
    `;

            tableExt.appendChild(newRow);
        }
    </script>
@endsection
