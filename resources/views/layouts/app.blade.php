<!DOCTYPE html>
<html lang='{{ str_replace('_', '-', app()->getLocale()) }}'>
    <head>
        <meta charset='utf-8'>
        <meta name='csrf-token' content='{{ csrf_token() }}'>

        <title>MedMazza @yield('title')</title>
        <link rel="icon" href="{{ asset('img/landing/favicon.png') }}">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
        <link rel="stylesheet" href="{{ asset('plugins/datatables/datatables.min.css') }}">
        <link rel="stylesheet" href="{{ asset('plugins/fullcalendar/dist/main.min.css') }}" />
        <!-- fontawesome icon -->
        <link rel="stylesheet" href="{{ asset('plugins/font-awesome/css/font-awesome.min.css') }}">
        <!-- material design icon -->
        {{-- <link rel="stylesheet" href="{{ asset('fonts/material/material-icons.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('plugins/material-icons/css/material-icons.css') }}">
        <!-- animation css -->
        <link rel="stylesheet" href="{{ asset('plugins/animate.css/animate.min.css') }}">
        <!-- vendor css -->
        <link rel="stylesheet" href="{{ asset('css/style.css') }}">

        <script src="{{ asset('plugins/jquery/js/jquery-3.7.1.min.js') }}"></script>
    </head>
    <body>
        <div class='content'>
            <!-- [ Pre-loader ] start -->
            <div class="loader-bg">
                <div class="loader-track">
                    <div class="loader-fill"></div>
                </div>
            </div>
            <!-- [ Pre-loader ] End -->
            @include('layouts.sidebar')
            @include('layouts.navbar')
            @yield('content')
        </div>

        <!-- Required Js -->
        <script src="{{ asset('js/vendor-all.min.js') }}"></script>
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>
        {{-- <script src="{{ asset('js/pcoded.min.js') }}"></script> --}}
        <script src="{{ asset('plugins/datta-able-dashboard/dist/assets/js/pcoded.js') }}"></script>
        {{-- <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script> --}}
        <script src="{{ asset('plugins/sweetalert2/js/sweetalert2.all.min.js') }}"></script>
        <script src="{{ asset('plugins/fullcalendar/dist/main.min.js') }}"></script>
        <script src="{{ asset('plugins/fullcalendar/dist/locales-all.min.js') }}"></script>
        <script src="{{ asset('plugins/datatables/datatables.min.js') }}" defer></script>
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                onOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        </script>
        @if (session('success'))
        <script>
            Toast.fire({
                icon: 'success',
                title: '{{ session('success') }}'
            })
        </script>
        @endif
        @if (session('error'))
            <script>
                Toast.fire({
                    icon: 'error',
                    title: '{{ session('error') }}'
                })
            </script>
        @endif
    </body>
</html>
