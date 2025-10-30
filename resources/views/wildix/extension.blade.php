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
        <div class="row mb-5">
            <form method="POST" action="{{ route('wildix.extension') }}">
                @csrf
                <br>
                <small><i><u>Numéros indisponibles :</u></i>
                    <br>
                    <ul>
                        <li>2 chiffres : 15, 17, 18.</li>
                        <li>3 chiffres : 100 à 199 (inclus).</li>
                        <li>4 chiffres : 3000 à 3999 (inclus) & 2222.</li>
                        <li>6 chiffres : 116000.</li>
                    </ul>
                </small>
                <button type="button" class="btn-add mt-2" onclick="addRow()"><i class="fa fa-plus" aria-hidden="true"></i>
                    Ajouter une ligne</button>
                <div class="row mt-2">
                    <table class="table table-hover table-bordered table-striped">
                        <thead style="text-align: center">
                            <tr>
                                <th>#Extension <span class="required-star">*</span></th>
                                <th>Nom affiché <span class="required-star">*</span></th>
                                <th>Email</th>
                                <th>#Présenté (appel sortant) <span class="required-star">*</span></th>
                                <th>Langue</th>
                                <th>Licence <span class="required-star">*</span></th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="tableExt">
                            @php
                                $extensions = old(
                                    'extensions',
                                    session('form_wildix.extensions', [
                                        [
                                            'extension' => '',
                                            'name' => '',
                                            'email' => '',
                                            'numPorte' => '',
                                            'language' => '',
                                            'licence' => '',
                                        ],
                                    ]),
                                );
                            @endphp

                            @foreach ($extensions as $i => $ext)
                                <tr>
                                    <td>
                                        <input type="number" min=1 name="extensions[{{ $i }}][extension]"
                                            value="{{ $ext['extension'] }}" class="form-control" required />
                                        @error("extensions.$i.extension")
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="text" name="extensions[{{ $i }}][name]"
                                            value="{{ $ext['name'] }}" class="form-control" required />
                                        @error("extensions.$i.name")
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="email" name="extensions[{{ $i }}][email]"
                                            value="{{ $ext['email'] }}" class="form-control" />
                                        @error("extensions.$i.email")
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <select name="extensions[{{ $i }}][numPorte]" class="form-control"
                                            required>
                                            <option value="" disabled
                                                {{ old('extensions.' . $i . '.language', $ext['language'] ?? '') == '' ? 'selected' : '' }}>
                                                --- Sélectionner le numéro porté ---</option>
                                            @foreach ($data['portes'] as $porte)
                                                <option value="{{ $porte['numero'] }}">
                                                    {{ $porte['numero'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error("extensions.$i.numPorte")
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <select name="extensions[{{ $i }}][language]" class="form-control"
                                            required>
                                            <option value="" disabled
                                                {{ old('extensions.' . $i . '.language', $ext['language'] ?? '') == '' ? 'selected' : '' }}>
                                                --- Choisir une langue ---</option>
                                            <option value="fr" selected
                                                {{ old('extensions.' . $i . '.language', $ext['language'] ?? '') == 'fr' ? 'selected' : '' }}>
                                                Français</option>
                                            <option value="en"
                                                {{ old('extensions.' . $i . '.language', $ext['language'] ?? '') == 'en' ? 'selected' : '' }}>
                                                Anglais</option>
                                            <option value="it"
                                                {{ old('extensions.' . $i . '.language', $ext['language'] ?? '') == 'it' ? 'selected' : '' }}>
                                                Italien</option>
                                            <option value="es"
                                                {{ old('extensions.' . $i . '.language', $ext['language'] ?? '') == 'es' ? 'selected' : '' }}>
                                                Espagnol</option>
                                        </select>
                                        @error("extensions.$i.language")
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td>
                                        <select name="extensions[{{ $i }}][licence]" class="form-control"
                                            required>
                                            <option value="" disabled
                                                {{ old('extensions.' . $i . '.licence', $ext['licence'] ?? '') == '' ? 'selected' : '' }}>
                                                --- Choisir une licence ---</option>
                                            <option value="premium"
                                                {{ old('extensions.' . $i . '.licence', $ext['licence'] ?? '') == 'premium' ? 'selected' : '' }}>
                                                Premium</option>
                                            <option value="business"
                                                {{ old('extensions.' . $i . '.licence', $ext['licence'] ?? '') == 'business' ? 'selected' : '' }}>
                                                Business</option>
                                            <option value="essential"
                                                {{ old('extensions.' . $i . '.licence', $ext['licence'] ?? '') == 'essential' ? 'selected' : '' }}>
                                                Essential</option>
                                            <option value="basic"
                                                {{ old('extensions.' . $i . '.licence', $ext['licence'] ?? '') == 'basic' ? 'selected' : '' }}>
                                                Basic</option>
                                            <option value="service"
                                                {{ old('extensions.' . $i . '.licence', $ext['licence'] ?? '') == 'service' ? 'selected' : '' }}>
                                                Service</option>
                                        </select>
                                        @error("extensions.$i.licence")
                                            <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </td>
                                    <td style="text-align: center">
                                        <button type="submit" name="delete" value="{{ $i }}"
                                            class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"
                                                style="padding: 5px;"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <button type="submit" style="float:right;" class="btn btn-success mt-5">Suivant</button>
            </form>
        </div>

        <script>
            let index = {{ count($extensions) }};

            function addRow() {
                const table = document.querySelector('#tableExt');
                const newRow = document.createElement('tr');

                newRow.innerHTML = `
            <td><input type="number" min=1 name="extensions[${index}][extension]" class="form-control" required /></td>
            <td><input type="text" name="extensions[${index}][name]" class="form-control" required /></td>
            <td><input type="email" name="extensions[${index}][email]" class="form-control" /></td>
            <td><select name="extensions[${index}][numPorte]" class="form-control" required>
                                        <option value="" selected disabled>-- Sélectionner le numéro porté --</option>
                                        @foreach ($data['portes'] as $porte)
                                            <option value="{{ $porte['numero'] }}">
                                                {{ $porte['numero'] }}
                                            </option>
                                        @endforeach
                                    </select></td>
            <td><select name="extensions[${index}][language]" class="form-control" required>
                                        <option value="fr" selected>
                                            Français</option>
                                        <option value="en">
                                            Anglais</option>
                                        <option value="it">
                                            Italien</option>
                                        <option value="es">
                                            Espagnol</option>
                                    </select></td>
            <td><select name="extensions[${index}][licence]" class="form-control" required>
                                        <option value="" selected disabled>
                                            --- Choisir une licence ---</option>
                                        <option value="premium">Premium</option>
                                        <option value="business">Business</option>
                                        <option value="essential">Essential</option>
                                        <option value="basic">Basic</option>
                                        <option value="service">Service</option>
                                    </select></td>
            <td style="text-align: center">
                <button type="button" onclick="removeRow(this)" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"
                                            style="padding: 5px;"></i></button>
                </td>
        `;
                table.appendChild(newRow);
                index++;
            }

            function removeRow(button) {
                button.closest('tr').remove();
            }
        </script>

    @endsection
