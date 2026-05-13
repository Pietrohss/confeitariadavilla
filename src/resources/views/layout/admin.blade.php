<!DOCTYPE html>
<html lang="pt-BR">

<head>
    @include('admin.partials.head')
</head>

<body class="layout-fixised ">

    <div class="page-wrapper">
        @include('partials.preloader')

        @include('partials.header')

        <main>
            @yield('content')
        </main>

        @include('partials.footer')

    </div>

    {{--  @include('partials.script'): Carrega os scripts usados em várias páginas --}}
    @include('partials.script')


    @stack('plugins') {{-- @stack: Área reservada, vai permitir que uma página específica injete um script --}}

    <script src="{{ asset('davilla/js/script.js') }}"></script>

    @stack('scripts') 

</body>

</html>