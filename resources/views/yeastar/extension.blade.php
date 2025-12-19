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
        <div class="row mb-5">
            <form method="POST" action="{{ route('yeastar.extension') }}">
                @csrf
                <br>
                <small><i><u>Numéros disponibles :</u></i>
                    <br>
                    <ul>
                        <li>4 chiffres : 1001 à 2999 et 4000 à 5999</li>
                    </ul>
                </small>
                <small><i><u>Numéros indisponibles :</u></i>
                    <br>
                    <ul class="mb-2">
                        <li>4 chiffres : 3000 à 3999 (inclus) & 2222.</li>
                    </ul>
                    <p><i><b>NB : </b>Pour utiliser la plage 3XXX, l'ajout d'un préfixe '0' est nécessaire. Exemple :
                            03XXX</i></p>
                </small>
                <br>
                <small class="">
                    <p><i class="fa-solid fa-triangle-exclamation"></i><b> Une fois le PBX livré, les numéros d'extensions
                            étant difficilement modifiables, toute
                            modification
                            de numérotation d'extension sera facturable</b></p>
                </small>
                <div class="d-flex flex-column flex-sm-row gap-2 align-items-sm-center mt-2">
                    <button type="button" class="btn-add" onclick="addRow()"><i class="fa fa-plus" aria-hidden="true"></i>
                        Ajouter une ligne</button>
                    <button type="button" class="btn-add-blue" onclick="toggleBulk()">
                        <i class="fa fa-layer-group" aria-hidden="true"></i> Ajouter en masse
                    </button>
                </div>
                <div class="row align-items-end mt-3" id="bulkWrapper" style="display: none;">
                    <div class="col-sm-4 col-md-3">
                        <label for="bulkStart" class="form-label">Extension de départ</label>
                        <input type="number" min="1" class="form-control" id="bulkStart" placeholder="Ex. 200" />
                    </div>
                    <div class="col-sm-4 col-md-3 mt-3 mt-sm-0">
                        <label for="bulkCount" class="form-label">Nombre de lignes</label>
                        <input type="number" min="1" class="form-control" id="bulkCount" placeholder="Ex. 5" />
                    </div>
                    <div class="col-sm-4 col-md-3 mt-3 mt-sm-0">
                        <button type="button" class="btn-add-blue" onclick="addRowsBulk()"><i class="fa fa-plus"
                                aria-hidden="true"></i></button>
                    </div>
                </div>
                <div class="row mt-2">
                    <table class="table table-hover table-bordered table-striped">
                        <thead style="text-align: center">
                            <tr>
                                <th>#Extension <span class="required-star">*</span></th>
                                <th>Nom affiché <span class="required-star">*</span></th>
                                <th>Email</th>
                                <th>#Présenté (appel sortant) <span class="required-star">*</span></th>
                                <th>Langue</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        @if (empty($extensions))
                            <tbody id="tableExt">
                                @php
                                    $extensions = old('extensions', session('form_yeastar.extensions', []));
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
                                        <td style="text-align: center">
                                            <button type="submit" name="delete" value="{{ $i }}"
                                                class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"
                                                    style="padding: 5px;"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
                    </table>
                </div>

                <a href="{{ route('yeastar.num_list') }}" style="float:left;" class="btn btn-secondary mt-5">Précédent</a>
                <button type="submit" style="float:right;" class="btn btn-success mt-5">Suivant</button>
            </form>
        </div>

        <script>
            let index = {{ count($extensions) }};

            function addRow(defaults = {}) {
                const table = document.querySelector('#tableExt');
                const newRow = document.createElement('tr');
                const currentIndex = index;

                newRow.innerHTML = `
            <td><input type="number" min=1 name="extensions[${currentIndex}][extension]" class="form-control" required /></td>
            <td><input type="text" name="extensions[${currentIndex}][name]" class="form-control" required /></td>
            <td><input type="email" name="extensions[${currentIndex}][email]" class="form-control" /></td>
            <td><select name="extensions[${currentIndex}][numPorte]" class="form-control" required>
                                        <option value="" selected disabled>-- Sélectionner le numéro porté --</option>
                                        @foreach ($data['portes'] as $porte)
                                            <option value="{{ $porte['numero'] }}">
                                                {{ $porte['numero'] }}
                                            </option>
                                        @endforeach
                                    </select></td>
            <td><select name="extensions[${currentIndex}][language]" class="form-control" required>
                                        <option value="fr" selected>
                                            Français</option>
                                        <option value="en">
                                            Anglais</option>
                                        <option value="it">
                                            Italien</option>
                                        <option value="es">
                                            Espagnol</option>
                                    </select></td>
            <td style="text-align: center">
                <button type="button" onclick="removeRow(this)" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"
                                            style="padding: 5px;"></i></button>
                </td>
        `;
                table.appendChild(newRow);
                applyDefaults(newRow, defaults);
                index++;
            }

            function removeRow(button) {
                button.closest('tr').remove();
            }

            function applyDefaults(row, defaults = {}) {
                const {
                    extension,
                    name,
                    email,
                    numPorte,
                    language,
                    licence
                } = defaults;

                const extensionInput = row.querySelector('input[name$="[extension]"]');
                const nameInput = row.querySelector('input[name$="[name]"]');
                const emailInput = row.querySelector('input[name$="[email]"]');
                const porteSelect = row.querySelector('select[name$="[numPorte]"]');
                const languageSelect = row.querySelector('select[name$="[language]"]');
                const licenceSelect = row.querySelector('select[name$="[licence]"]');

                if (extensionInput && typeof extension !== 'undefined') extensionInput.value = extension;
                if (nameInput && typeof name !== 'undefined') nameInput.value = name;
                if (emailInput && typeof email !== 'undefined') emailInput.value = email;
                if (porteSelect && typeof numPorte !== 'undefined' && porteSelect.querySelector(
                        `option[value="${numPorte}"]`)) {
                    porteSelect.value = numPorte;
                }
                if (languageSelect) {
                    if (typeof language !== 'undefined' && languageSelect.querySelector(`option[value="${language}"]`)) {
                        languageSelect.value = language;
                    } else if (!languageSelect.value && languageSelect.querySelector('option[value="fr"]')) {
                        languageSelect.value = 'fr';
                    }
                }
                if (licenceSelect && typeof licence !== 'undefined' && licenceSelect.querySelector(
                        `option[value="${licence}"]`)) {
                    licenceSelect.value = licence;
                }
            }

            function getEmptyRows() {
                const rows = document.querySelectorAll('#tableExt tr');
                return Array.from(rows).filter((row) => {
                    const fields = row.querySelectorAll('input, select');
                    return fields.length && Array.from(fields).every((field) => !field.value);
                });
            }

            function toggleBulk() {
                const bulkWrapper = document.getElementById('bulkWrapper');
                const startInput = document.getElementById('bulkStart');
                const countInput = document.getElementById('bulkCount');
                const isHidden = bulkWrapper.style.display === 'none';
                bulkWrapper.style.display = isHidden ? '' : 'none';
                if (!isHidden) {
                    startInput.value = '';
                    countInput.value = '';
                } else {
                    startInput.focus();
                }
            }

            function addRowsBulk() {
                const startInput = document.getElementById('bulkStart');
                const countInput = document.getElementById('bulkCount');
                const start = parseInt(startInput.value, 10);
                const count = parseInt(countInput.value, 10);

                if (Number.isNaN(start) || Number.isNaN(count) || count <= 0) {
                    alert('Veuillez saisir un numéro de départ et un nombre de lignes valides.');
                    return;
                }

                const emptyRows = getEmptyRows();
                emptyRows.forEach((row) => row.remove());

                index = document.querySelectorAll('#tableExt tr').length;

                for (let offset = 0; offset < count; offset++) {
                    const defaults = {
                        extension: start + offset
                    };

                    addRow({
                        ...defaults
                    });
                }
            }
        </script>

    @endsection
