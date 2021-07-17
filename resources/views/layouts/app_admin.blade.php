<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>

    <link rel="icon" href="{{ asset('/data/logo/logo.png') }}">

    <!-- INIT STYLE -->
    @include('theme.default.source.script_source')
    @include('theme.default.source.css_source')
    @yield('import_css')

</head>
<body class="sidebar-mini layout-navbar-fixed sidebar-collapse overflow-hidden" style="background: #f4f6f9;">
    <style>
        @yield('css')
    </style>

    @yield('bg')

    @include('theme.default.header')
    @include('theme.default.sidenav')

    <div class="wrapper">

        @yield('title')
        <div class="content-wrapper mt-0 mb-0 p-3">
            @yield('content')
        </div>
    </div>

    <section class="modal_group">
        @yield('modal')
    </section>

    <!-- INIT SCRIPTS -->
    @yield('import_script')

    <script>
        @yield('script')
    </script>

    @yield('message')
</body>
</html>
