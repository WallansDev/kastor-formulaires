@extends('layouts.base')

@section('title', 'SVI')

@section('content')
    <div class="container">

        {{-- DEBUG --}}
        <a href="{{ route('debug.session') }}">Dump Session</a>

        <div class="row">
            <form action="{{ route('form.reset') }}" method="POST">
                @csrf
                <button type="submit" style="float:right;" class="btn btn-outline-danger"><i class="fa fa-trash"
                        aria-hidden="true" style="color: darkred;"></i> Vider la session</button>
            </form>

            <div class=" text-center col-12 mt-3">
                @include('form.header')
            </div>
        </div>

        <form method="POST" action="{{ route('form.svi') }}">
            @csrf
            <div class="row mt-3">
                <div class="col-6 d-flex align-items-center mb-3">
                    <h4 class="me-3 mb-0">SVI</h4>
                    <div class="form-check form-switch">
                        <input type="hidden" name="svi_enabled" value="0">

                        <input class="form-check-input" type="checkbox" role="switch" id="switchCheckDefault"
                            name="svi_enabled" value="1"
                            {{ old('svi_enabled', session('form.svi_enabled', false)) ? 'checked' : '' }}>

                        <label class="form-check-label" for="switchCheckDefault"></label>
                    </div>

                    <button type="button" class="btn btn-success" id="add-svi-option">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                    &ensp;
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save" aria-hidden="true"></i>
                    </button>
                </div>

                <div id="svi-content" style="display: none;">

                    <div class="mt-3" id="svi-options">
                        <!-- Options dynamiques -->
                    </div>
                </div>

            </div>
            <a href="{{route('form.infos')}}" style="float:right;" class="btn btn-success">Suivant</a>
        </form>
    </div>
@endsection

@section('script-content')
<script>
        document.addEventListener('DOMContentLoaded', () => {
            const sviEnabled = @json(session('form.svi_enabled', false));
            const sviOptions = @json(session('form.svi_options', []));

            const sviSwitch = document.getElementById('switchCheckDefault');
            const sviContent = document.getElementById('svi-content');
            const sviOptionsContainer = document.getElementById('svi-options');
            const addOptionBtn = document.getElementById('add-svi-option');

            // sviSwitch.checked = sviEnabled;
            sviContent.style.display = sviEnabled ? 'block' : 'none';

            // Initialisation avec les options en session
            sviOptions.forEach(option => {
                addOption(option.nom);
            });
            sviContent.style.display = sviSwitch.checked ? 'block' : 'none';
            addOptionBtn.addEventListener('click', () => {
                addOption();
            });

            function addOption(nom = '', ordre = null) {
                const optionDiv = document.createElement('div');
                optionDiv.classList.add('row', 'mb-2');

                const currentIndex = sviOptionsContainer.children.length;
                const newOrdre = ordre || currentIndex + 1;

                optionDiv.innerHTML = `
        <div class="col-1 d-flex align-items-center">
            <input type="hidden" name="svi[${currentIndex}][ordre]" class="ordre-input" value="${newOrdre}">
            <span class="form-control-plaintext ordre-display">Choix : ${newOrdre}</span>
        </div>
        <div class="col-3">
            <input type="text" name="svi[${currentIndex}][nom]" class="form-control" placeholder="Nom du service" value="${nom}" required>
        </div>
        <div class="col-md-3 d-flex align-items-center">
            <button type="button" class="btn btn-danger btn-sm remove-svi-option">Supprimer</button>
        </div>
    `;

                optionDiv.querySelector('.remove-svi-option').addEventListener('click', () => {
                    optionDiv.remove();
                    reindexOrdres();
                });

                sviOptionsContainer.appendChild(optionDiv);
            }

            function reindexOrdres() {
                const allOptionDivs = sviOptionsContainer.querySelectorAll('.row');
                allOptionDivs.forEach((div, index) => {
                    const ordreInput = div.querySelector('.ordre-input');
                    const ordreDisplay = div.querySelector('.ordre-display');
                    const nomInput = div.querySelector('input.form-control');

                    ordreInput.name = `svi[${index}][ordre]`;
                    ordreInput.value = index + 1;

                    nomInput.name = `svi[${index}][nom]`;
                    ordreDisplay.textContent = `Choix : ${index + 1}`;
                });
            }

            sviSwitch.addEventListener('change', () => {
                sviContent.style.display = sviSwitch.checked ? 'block' : 'none';
            });
        });
    </script>
@endsection