<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Asset Management System by Partnership">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | Asset Management System by Partnership</title>

    <!-- Sweetalert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/Material/assets/css/nucleo-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/Material/assets/css/nucleo-svg.css') }}" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />

    <!-- Styles / Scripts -->
    {{-- @vite(['resources/js/app.js']) --}}
    <!-- Theme Style -->
    <link id="pagestyle" href="{{ asset('assets/Material/assets/css/material-dashboard.css?v=3.2.0') }}"
        rel="stylesheet" />
    <!-- Our style -->
    <link rel="stylesheet" href="{{ asset('assets/CSS/style.css') }}">

    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />

    @stack('css')
</head>

<body class="g-sidenav-show  bg-gray-100">
    <aside class="sidenav navbar navbar-vertical navbar-expand-xs border-radius-lg fixed-start ms-2 bg-white my-2"
        id="sidenav-main">
        <div class="sidenav-header">
            <i class="fas fa-times p-3 cursor-pointer text-dark opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand px-4 py-3 m-0" href="{{ route('dashboard') }}">
                <img src="{{ asset('assets/Image/logo/Main.png') }}" class="navbar-brand-img" height="50"
                    alt="main_logo">
            </a>
        </div>

        <hr class="horizontal dark mt-0 mb-2">

        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('dashboard') }}">
                        <i class="material-symbols-rounded opacity-5">dashboard</i>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="#">
                        <i class="material-symbols-rounded opacity-5">monitoring</i>
                        <span class="nav-link-text ms-1">Report</span>
                    </a>
                </li>

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Asset
                    </h6>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark dropdown-toggle" href="#" id="menuDropdown" role="button"
                        data-bs-toggle="collapse" data-bs-target="#submenu" aria-expanded="false">
                        <i class="material-symbols-rounded opacity-5">inventory</i>
                        <span class="nav-link-text ms-1">Asset</span>
                    </a>
                    <div class="collapse" id="submenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('asset.index') }}">Asset List</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="#">Maintenance</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="#">Mutasion</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="#">Disposal</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark dropdown-toggle" href="#" id="menuDropdown" role="button"
                        data-bs-toggle="collapse" data-bs-target="#history" aria-expanded="false">
                        <i class="material-symbols-rounded opacity-5">history</i>
                        <span class="nav-link-text ms-1">History</span>
                    </a>
                    <div class="collapse" id="history">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="#">Changes History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="#">Mutation History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="#">Location History</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="#">
                        <i class="material-symbols-rounded opacity-5">task</i>
                        <span class="nav-link-text ms-1">Approval</span>
                    </a>
                </li>

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Data
                    </h6>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('tag.index') }}">
                        <i class="material-symbols-rounded opacity-5">sell</i>
                        <span class="nav-link-text ms-1">Tag RFID</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark dropdown-toggle" href="#" id="menuDropdown" role="button"
                        data-bs-toggle="collapse" data-bs-target="#masterdata" aria-expanded="false">
                        <i class="material-symbols-rounded opacity-5">database</i>
                        <span class="nav-link-text ms-1">Master Data</span>
                    </a>
                    <div class="collapse" id="masterdata">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('kecamatan.index') }}">Kecamatan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('sekolah.index') }}">Sekolah</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('user.index') }}">Pengguna</a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        <div class="sidenav-footer position-absolute w-100 bottom-0 ">
            <div class="mx-3">
                <a class="btn bg-gradient-danger w-100 rounded-5" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="material-symbols-rounded opacity-5">power_settings_new</i>
                    Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        @yield('content')
    </main>


    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

    <!--   Core JS Files   -->
    <script src="{{ asset('assets/Material/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/Material/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/Material/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/Material/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/Material/assets/js/plugins/chartjs.min.js') }}"></script>

    @stack('scripts')

    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets/Material/assets/js/material-dashboard.min.js?v=3.2.0') }}"></script>

    <!-- Active Class -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var url = window.location.href;

            document.querySelectorAll(".nav-link").forEach(function(link) {
                if (link.href === url || url.startsWith(link.href)) {
                    link.classList.add("active", "text-white", "bg-gradient-info");
                    let parent = link.closest(".collapse");
                    if (parent) {
                        parent.classList.add("show");
                        let toggle = parent.previousElementSibling;
                        if (toggle) toggle.classList.add("active");
                    }
                }
            });
        });
    </script>

    <!-- Sweetalert2 -->
    <script>
        const Toast = Swal.mixin({
            toast: true,
            position: 'top',
            iconColor: 'white',
            customClass: {
                popup: 'colored-toast'
            },
            showConfirmButton: false,
            timer: 5000,
            timerProgressBar: true
        })

        @if (session('pesan'))
            @switch(session('level-alert'))
                @case('alert-success')
                Toast.fire({
                    icon: 'success',
                    title: '{{ Session::get('pesan') }}'
                })
                @break

                @case('alert-danger')
                Toast.fire({
                    icon: 'error',
                    title: '{{ Session::get('pesan') }}'
                })
                @break

                @case('alert-warning')
                Toast.fire({
                    icon: 'warning',
                    title: '{{ Session::get('pesan') }}'
                })
                @break

                @case('alert-question')
                Toast.fire({
                    icon: 'question',
                    title: '{{ Session::get('pesan') }}'
                })
                @break

                @default
                Toast.fire({
                    icon: 'info',
                    title: '{{ Session::get('pesan') }}'
                })
            @endswitch
        @endif
        @if (count($errors) > 0)
            @foreach ($errors->all() as $error)
                Toast.fire({
                    icon: 'error',
                    title: '{{ $error }}'
                })
            @endforeach
        @endif
    </script>
</body>

</html>
