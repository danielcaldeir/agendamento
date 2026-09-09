<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="Nosso principal objetivo é que nossos pacientes encontrem o médico perfeito e agende uma consulta do modo mais fácil possível. Essa jornada precisa ser agradável, por isso sempre estamos dispostos a ajudar." />
        <meta name="keywords" content="medico, agendar, consulta, online, agendar consulta, clinica"/>
        <meta name="author" content="Eduardo Nascimento"/>
        <title>MedMazza | Agendamento de Consultas Online</title>
        <link rel="icon" href="{{ asset('img/landing/favicon.png') }}">
        <!-- Bootstrap CSS -->
        {{-- <link rel="stylesheet" href="{{ asset('css/landing/bootstrap.min.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('plugins/bootstrap/css/bootstrap.min.css') }}">
        <!-- animate CSS -->
        {{-- <link rel="stylesheet" href="{{ asset('css/landing/animate.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('plugins/animate.css/animate.min.css') }}">
        <!-- owl carousel CSS -->
        {{-- <link rel="stylesheet" href="{{ asset('css/landing/owl.carousel.min.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('plugins/OwlCarousel/dist/assets/owl.carousel.min.css') }}">
        <!-- themify CSS -->
        <!-- <link rel="stylesheet" href="{{ asset('css/landing/themify-icons.css') }}"> -->
        <link rel="stylesheet" href="{{ asset('plugins/themify-icons/themify-icons.css') }}">
        <!-- flaticon CSS -->
        {{-- <link rel="stylesheet" href="{{ asset('css/landing/flaticon.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('plugins/flaticon-uicons/css/all/all.css') }}">
        <!-- magnific popup CSS -->
        {{-- <link rel="stylesheet" href="{{ asset('css/landing/magnific-popup.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('plugins/Magnific-Popup/dist/magnific-popup.css') }}">
        <!-- nice select CSS -->
        {{-- <link rel="stylesheet" href="{{ asset('css/landing/nice-select.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('plugins/jquery-nice-select/css/nice-select.css') }}">
        <!-- swiper CSS -->
        {{-- <link rel="stylesheet" href="{{ asset('css/landing/slick.css') }}"> --}}
        <link rel="stylesheet" href="{{ asset('plugins/slick/slick.css') }}">
        <!-- style CSS -->
        <link rel="stylesheet" href="{{ asset('css/landing/style.css') }}">
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
            @include('layouts.pageHeader')
            @yield('content')
            @include('layouts.pageFooter')
        </div>

        <!-- jquery plugins here-->
        {{-- <script src="{{ asset('js/landing/jquery.min.js') }}"></script> --}}
        <script src="{{ asset('plugins/jquery/js/jquery-3.7.1.min.js') }}"></script>

        <!-- popper js -->
        {{-- <script src="{{ asset('js/landing/popper.min.js') }}"></script> --}}
        <script src="{{ asset('plugins/popper.js/popper.min.js') }}"></script>

        <!-- bootstrap js -->
        {{-- <script src="{{ asset('js/landing/bootstrap.min.js') }}"></script> --}}
        <script src="{{ asset('plugins/bootstrap/js/bootstrap.min.js') }}"></script>

        <!-- owl carousel js -->
        {{-- <script src="{{ asset('js/landing/owl.carousel.min.js') }}"></script> --}}
        <script src="{{ asset('plugins/OwlCarousel/dist/owl.carousel.min.js') }}"></script>
        {{-- <script src="{{ asset('js/landing/jquery.nice-select.min.js') }}"></script> --}}
        <script src="{{ asset('plugins/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script>

        <!-- contact js -->
        {{-- <script src="{{ asset('js/landing/jquery.ajaxchimp.min.js') }}"></script> --}}
        <script src="{{ asset('plugins/jquery-ajaxchimp/jquery.ajaxchimp.min.js') }}"></script>
        {{-- <script src="{{ asset('js/landing/jquery.form.js') }}"></script> --}}
        <script src="{{ asset('plugins/jquery-form/dist/jquery.form.min.js') }}"></script>
        {{-- <script src="{{ asset('js/landing/jquery.validate.min.js') }}"></script> --}}
        <script src="{{ asset('plugins/jquery-validation/dist/jquery.validate.min.js') }}"></script>
        <script src="{{ asset('js/landing/mail-script.js') }}"></script>
        <script src="{{ asset('js/landing/contact.js') }}"></script>

        <!-- custom js -->
        <script src="{{ asset('js/landing/custom.js') }}"></script>
    </body>
</html>
