@extends('layouts.base')

@section('title', "Groupes d'appel et File d'attente")

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

        <form method="POST" action="{{ route('yeastar.call_group') }}">
            @csrf
            <div class="row mt-5">

                {{-- RING GROUP --}}
                <div class="col-6">
                    <h4>Créer un groupe d'appel</h4>

                    @error('cgName')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @error('cg_type')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <div class="row mb-3">

                        <div class="col">
                            <input type="text" name="cgName" placeholder="Nom du groupe" class="form-control"
                                value="{{ old('cgName') }}">
                        </div>
                        <div class="col">
                            <select name="cg_type" class="form-control mb-3">
                                <option value="" disabled selected>-- Choisir une stratégie de sonnerie --</option>
                                <option value="all_10">Appeler les 10 (all_10)</option>
                                <option value="linear">Linéaire (linear)</option>
                                <option value="memory_hunt">Memory Hunt (memory_hunt)</option>
                                <option value="custom">Custom (custom)</option>
                            </select>
                        </div>
                        <div id="memoryHuntFields" class="col" style="display: none;">
                            <input type="number" min="1" max="100" title="Temps maximum de sonnerie"
                                name="cg_ring_timeout" class="form-control" placeholder="Temps maximum de sonnerie">
                        </div>
                        <div class="col">
                            <select name="ext_selectionne_new[]" class="form-control" multiple size="5">
                                <option value="" disabled>--- Liste d'extensions ---</option>
                                @foreach ($extensions as $ext)
                                    <option value="{{ $ext['extension'] }}">{{ $ext['extension'] }} -
                                        {{ $ext['surname'] . ' ' . $ext['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs
                                extensions</small>
                        </div>
                        <div class="col-auto">
                            <button class="btn btn-primary" style="float:right" name="action_type" value="add_group"><i
                                    class="fa fa-plus"></i></button>
                        </div>
                    </div>

                </div>
                <div class="col"></div>

                {{-- QUEUES --}}
                <div class="col-5">
                    <h4>Créer une file d'attente d'appel</h4>
                    <div class="row mb-3">

                        <div class="col">
                            <input type="text" name="qName" placeholder="Nom de la file d'attente" class="form-control"
                                value="{{ old('qName') }}">
                        </div>
                        <div class="col">
                            <select name="q_ring_strategy" class="form-control mb-3">
                                <option value="" disabled selected>-- Choisir une stratégie de sonnerie --</option>
                                <option value="ring_all">Tout le monde (ring_all)</option>
                                <option value="least_recent">Le moins récent (least_recent)</option>
                                <option value="fewest_recent">Le moins d'appels (fewest_recent)</option>
                                <option value="random">Aléatoire (random)</option>
                                <option value="round_robin_memory">À tour de rôle avec mémoire (round_robin_memory)</option>
                                <option value="linear">Linéaire (linear)</option>
                            </select>
                        </div>
                        <div class="col">
                            <select name="ext_selectionne_new[]" class="form-control" multiple size="5">
                                <option value="" disabled>--- Liste d'extensions ---</option>
                                @foreach ($extensions as $ext)
                                    <option value="{{ $ext['extension'] }}">{{ $ext['extension'] }} -
                                        {{ $ext['surname'] . ' ' . $ext['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs
                                extensions</small>
                        </div>
                        <div class="col-auto"> <button class="btn btn-primary" style="float:right" name="action_type"
                                value="add_queue"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-7">
                    <h4>Associer une extension à un groupe d'appel ou file d'attente</h4>
                    @error('cg_selectionne')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    @error('ext_selectionne')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                    <div class="row mb-3">
                        <div class="col">
                            <select name="cg_selectionne" class="form-control">
                                <option value="" disabled selected>-- Choisir le groupe ou la file --
                                </option>
                                @if (count($callGroups) > 0)
                                    <optgroup label="Groupes d'appel">
                                        @foreach ($callGroups as $group)
                                            @php
                                                $isValidGroup = is_array($group) && isset($group['name']);
                                                $groupName = $isValidGroup ? (string) $group['name'] : '';
                                                $groupType = $isValidGroup ? (string) ($group['type'] ?? '') : '';
                                            @endphp
                                            @continue(!$isValidGroup)
                                            <option value="{{ $groupName }}" data-type="{{ $groupType }}">
                                                {{ $groupName }}
                                                ({{ $groupType }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endif
                                @if (count($queues) > 0)
                                    <optgroup label="File d'attente">
                                        @foreach ($queues as $queue)
                                            <option value="{{ $queue['name'] }}">{{ $queue['name'] }}</option>
                                        @endforeach
                                    </optgroup>
                                @endif
                            </select>
                        </div>
                        <div id="customFields" class="col" style="display: none;">
                            <div class="row g-2">
                                <div class="col-12 col-md-6">
                                    <input type="number" min="1" max="100" title="Délai de sonnerie"
                                        name="ext_ring_delay" class="form-control" placeholder="Délai de sonnerie">
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="number" min="1" max="100"
                                        title="Temps maximum de sonnerie" name="ext_ring_timeout" class="form-control"
                                        placeholder="Temps maximum de sonnerie">
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <select name="ext_selectionne[]" class="form-control" multiple size="5">
                                <option value="" disabled>--- Liste d'extensions ---</option>
                                @foreach ($extensions as $ext)
                                    <option value="{{ $ext['extension'] }}">{{ $ext['extension'] }} -
                                        {{ $ext['surname'] . ' ' . $ext['name'] }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs
                                extensions</small>
                        </div>

                        <div class="col-auto">
                            <button class="btn btn-primary" name="action_type" value="add_ext" title="Ajouter">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>


                    </div>
                </div>
            </div>
            <hr>

            <div class="row mt-5">
                <div class="col-5">
                    <h4>Groupes d'appels et File d'attente existants</h4>
                    <p class="mt-3">--- Groupe(s) d'appel ---</p>
                    @forelse ($callGroups as $index => $group)
                        @php
                            $isValidGroup = is_array($group) && isset($group['name']);
                            $groupName = $isValidGroup ? (string) $group['name'] : '';
                            $groupType = $isValidGroup ? (string) ($group['type'] ?? '') : '';
                        @endphp
                        @continue(!$isValidGroup)
                        <div class="mb-3 p-2 border rounded">
                            <strong>{{ $groupName }}</strong> (Stratégie : {{ $groupType }})
                            @if (($group['type'] ?? null) === 'memory_hunt' && !empty($group['ring_timeout']))
                                <div class="text-muted small">délai d'attente : {{ $group['ring_timeout'] }}s</div>
                            @endif
                            <button class="btn btn-sm btn-danger float-end" name="action_type"
                                value="delete_group_{{ $index }}">
                                <i class="fa fa-trash"></i>
                            </button>

                            @if (!empty($group['ext']))
                                <ul class="mt-2">
                                    @foreach ($group['ext'] as $extIndex => $ext)
                                        @php
                                            $displayMeta = '';
                                            if (($group['type'] ?? null) === 'custom') {
                                                $settings = $group['ext_settings'][$ext] ?? null;
                                                if ($settings) {
                                                    $parts = [];
                                                    if (
                                                        isset($settings['ring_delay']) &&
                                                        $settings['ring_delay'] !== null &&
                                                        $settings['ring_delay'] !== ''
                                                    ) {
                                                        $parts[] = 'délai : ' . $settings['ring_delay'] . 's';
                                                    } else {
                                                        $parts[] = 'délai : à configurer';
                                                    }
                                                    if (
                                                        isset($settings['ring_timeout']) &&
                                                        $settings['ring_timeout'] !== null &&
                                                        $settings['ring_timeout'] !== ''
                                                    ) {
                                                        $parts[] =
                                                            "délai d'attente : " . $settings['ring_timeout'] . 's';
                                                    } else {
                                                        $parts[] = "délai d'attente : à configurer";
                                                    }
                                                    $displayMeta = implode(' | ', $parts);
                                                }
                                            }
                                        @endphp
                                        <li>
                                            {{ $ext }}
                                            @if (!empty($displayMeta))
                                                <span class="text-muted"> — {{ $displayMeta }}</span>
                                            @endif
                                            <button class="btn btn-sm btn-outline-danger" name="action_type"
                                                value="delete_ext|{{ $groupName }}|{{ $extIndex }}">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">Aucune extension assignée.</p>
                            @endif
                        </div>
                    @empty
                        <p>Aucun groupe d’appel pour le moment.</p>
                    @endforelse

                    <p class="mt-3">--- File(s) d'attente ---</p>

                    @forelse ($queues as $index => $queue)
                        @php
                            $isValidQueue = is_array($queue) && isset($queue['name']);
                            $queueName = $isValidQueue ? (string) $queue['name'] : '';
                        @endphp
                        @continue(!$isValidQueue)
                        <div class="mb-3 p-2 border rounded">
                            <strong>{{ $queueName }}</strong>
                            @if (!empty($queue['strategy']))
                                <div class="text-muted small">stratégie : {{ $queue['strategy'] }}</div>
                            @endif
                            <button class="btn btn-sm btn-danger float-end" name="action_type"
                                value="delete_queue_{{ $index }}">
                                <i class="fa fa-trash"></i>
                            </button>

                            @if (!empty($queue['ext']))
                                <ul class="mt-2">
                                    @foreach ($queue['ext'] as $extIndex => $ext)
                                        <li>
                                            {{ $ext }}
                                            <button class="btn btn-sm btn-outline-danger" name="action_type"
                                                value="delete_ext|{{ $queueName }}|{{ $extIndex }}">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">Aucune extension assignée.</p>
                            @endif
                        </div>
                    @empty
                        <p>Aucune file d'attente pour le moment.</p>
                    @endforelse
                </div>
            </div>

            <a href="{{ route('yeastar.extension') }}" style="float:left;" class="btn btn-secondary mt-5">Précédent</a>
            <button type="submit" style="float:right;" class="btn btn-success mt-5 mb-5">Suivant</button>

        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cgTypeSelect = document.querySelector('select[name="cg_type"]');
            const memoryHuntFields = document.getElementById('memoryHuntFields');
            const customFields = document.getElementById('customFields');
            const groupSelect = document.querySelector('select[name="cg_selectionne"]');

            function toggleMemoryHuntFields() {
                if (!cgTypeSelect || !memoryHuntFields) return;
                memoryHuntFields.style.display = cgTypeSelect.value === 'memory_hunt' ? '' : 'none';
            }

            function toggleCustomFields() {
                if (!customFields || !groupSelect) return;
                const selectedOption = groupSelect.options[groupSelect.selectedIndex];
                const selectedType = selectedOption ? selectedOption.getAttribute('data-type') : null;
                customFields.style.display = selectedType === 'custom' ? '' : 'none';
            }

            toggleMemoryHuntFields();
            toggleCustomFields();

            if (cgTypeSelect) {
                cgTypeSelect.addEventListener('change', toggleMemoryHuntFields);
            }
            if (groupSelect) {
                groupSelect.addEventListener('change', toggleCustomFields);
            }
        });
    </script>
@endsection
