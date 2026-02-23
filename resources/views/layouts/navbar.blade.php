<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: white;">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center gap-2" style="color: black;" href="{{ route('home') }}">
            <img id="kastor-logo" src="{{ asset('images/kastor.png') }}" alt="Logo" width="60" height="60">
            {{ strtoupper(config('app.name', 'Kastor Formulaires')) }}
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3 py-2 rounded" href="#" style="color: grey;"
                        id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-phone me-1"></i> Formulaires de création de PBX
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('wildix.general_info') }}">
                                <img src="{{ asset('images/wildix.png') }}" height="20" alt="Wildix">
                                Je souhaites créer un PBX WILDIX
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider my-1">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2"
                                href="{{ route('yeastar.general_info') }}">
                                <img src="{{ asset('images/yeastar.png') }}" height="20" alt="Yeastar">
                                Je souhaites créer un PBX VOKALISE
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
