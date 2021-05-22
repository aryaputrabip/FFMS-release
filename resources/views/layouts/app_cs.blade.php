<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    <!-- INIT STYLE -->
    @include('theme.default.source.css_source')
    @yield('import_css')

</head>
<body class="sidebar-mini layout-navbar-fixed overflow-hidden" style="background: url({{ asset('/img/bg/bg-2.jpg') }}); background-position: top center; background-size: cover;">
    <style>
        @yield('css')
    </style>

    @yield('bg')
    <img src="{{ asset('/img/bg/bg-overlay.png') }}" style="position:absolute; bottom: 0; left: 0; opacity: 0.25;">

    @include('theme.default.header_nosidenav')
    <div class="wrapper">

        @yield('title')
        <div class="container-fluid container-wrapper mt-0 mb-0 p-3 vh-100">
            @yield('content')
        </div>
    </div>

    <section class="modal_group">
        @yield('modal')
    </section>

    <!-- INIT SCRIPTS -->
    @include('theme.default.source.script_source')
    @yield('import_script')

    <script>
        @yield('script')
    </script>

    @yield('message')
</body>
</html>
