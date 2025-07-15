<!doctype html>
<html lang="fr">

<head>

    <!-- 
*****************************
*     Timothé VAQUIÉ        *
*     Version : 1.0         *
*     Date : 15/07/2025     *
*****************************
-->

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="/images/kastor.ico">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- BOOSTRAP CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- CSS --}}
    <link rel="stylesheet" href="{{asset('css/stylebase.css')}}">

    {{-- ADDITIONAL CSS --}}
    @yield('head-content')
</head>

<body>
    @include('layouts.navbar')
    @yield('content')

    {{-- JS BOOTSTRAP --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>

    {{-- Additional JS --}}
    @yield('script-content')
</body>

</html>
