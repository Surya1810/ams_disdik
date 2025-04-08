<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') | Asset Management System by Partnership</title>

    <!-- Sweetalert2 -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('assets/FontAwesome/css/all.min.css') }}">
    <!-- Theme Style -->
    <link id="pagestyle" href="{{ asset('assets/Argon/assets/css/argon-dashboard.css?v=2.1.0') }}" rel="stylesheet" />
    <!-- Our style -->
    <link rel="stylesheet" href="{{ asset('assets/CSS/style.css') }}">
    <!-- Datatables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.8/css/dataTables.dataTables.css" />

    @stack('css')
</head>

<body class="g-sidenav-show bg-gray-100">
    <!-- Background -->
    <div class="position-absolute w-100 min-height-300 top-0"
        style="background-image: url('{{ asset('assets/Image/background/Pattern_Dark.webp') }}'); background-position-y: 50%;">
        <span class="mask opacity-6"></span>
    </div>
    <aside
        class="sidenav bg-white navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-4"
        id="sidenav-main">
        <div class="sidenav-header d-flex justify-content-center align-items-center">
            <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none"
                aria-hidden="true" id="iconSidenav"></i>
            <a class="navbar-brand m-0" href="{{ route('dashboard') }}">
                <img src="{{ asset('assets/Image/logo/Dark.webp') }}" class="navbar-brand-img" alt="main_logo">
            </a>
        </div>
        <hr class="horizontal dark my-0">

        <!-- Section Profile -->
        <div class="d-flex align-items-center justify-content-center my-3">
            <img src="{{ asset('assets/Image/profile/profile.jpg') }}" alt="Avatar" class="rounded-circle shadow me-2"
                width="45" height="45">
            <div class="d-flex flex-column">
                <p class="text-dark fw-bold mb-0">{{ Auth::user()->name }}</p>
                <small class="text-dark">{{ Auth::user()->role->name }}</small>
            </div>
        </div>


        <hr class="horizontal dark mt-0">

        <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('dashboard') }}">
                        <i class="fa-solid fa-house"></i>
                        <span class="nav-link-text ms-1">Dashboard</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('report.index') }}">
                        <i class="fa-solid fa-chart-column"></i>
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
                        <i class="fa-solid fa-boxes-packing"></i>
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
                                <a class="nav-link text-dark" href="#">Mutation</a>
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
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <span class="nav-link-text ms-1">History</span>
                    </a>
                    <div class="collapse" id="history">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('changes.history') }}">Changes History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('mutation.history') }}">Mutation
                                    History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-dark" href="{{ route('location.history') }}">Location
                                    History</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('scan.index') }}">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span class="nav-link-text ms-1">Scan</span>
                    </a>
                </li>

                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="#">
                            <i class="fa-solid fa-clipboard-check"></i>
                            <span class="nav-link-text ms-1">Approval</span>
                        </a>
                    </li>
                @endif

                <li class="nav-item mt-3">
                    <h6 class="ps-4 ms-2 text-uppercase text-xs text-dark font-weight-bolder opacity-5">Data
                    </h6>
                </li>

                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('tag.index') }}">
                        <i class="fa-solid fa-tags"></i>
                        <span class="nav-link-text ms-1">Tag RFID</span>
                    </a>
                </li>

                @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
                    <li class="nav-item">
                        <a class="nav-link text-dark dropdown-toggle" href="#" id="menuDropdown"
                            role="button" data-bs-toggle="collapse" data-bs-target="#masterdata"
                            aria-expanded="false">
                            <i class="fa-solid fa-database"></i>
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
                @endif
            </ul>
        </div>
        <div class="sidenav-footer position-absolute w-100 bottom-0 text-center">
            <div class="mx-3 mt-3">
                <a class="btn bg-gradient-danger rounded-partner" href="{{ route('logout') }}"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fa-solid fa-power-off"></i>
                    Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </aside>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl text-white"
            id="navbarBlur" data-scroll="true">
            <div class="container-fluid py-1 px-3">

                @yield('navbar')

                <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0" id="navbar">
                    <div class="ms-md-auto pe-md-3 d-flex align-items-center">
                    </div>
                    <ul class="navbar-nav justify-content-end">
                        <li class="nav-item d-xl-none ps-3 d-flex align-items-center me-3">
                            <a href="javascript:;" class="nav-link text-white p-0" id="iconNavbarSidenav">
                                <div class="sidenav-toggler-inner">
                                    <i class="sidenav-toggler-line bg-white"></i>
                                    <i class="sidenav-toggler-line bg-white"></i>
                                    <i class="sidenav-toggler-line bg-white"></i>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item d-flex align-items-center">
                            <div class="form-check form-switch d-flex align-items-center">
                                <input class="form-check-input" type="checkbox" id="toggle-mode"
                                    onclick="toggleDarkSidebar(this)">
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="container-fluid py-4">

            @yield('content')

            <!-- Footer -->
            <footer class="footer pt-3">
                <div class="container-fluid">
                    <div class="row align-items-center justify-content-lg-between">
                        <div class="col-lg-6 mb-lg-0 mb-4">
                            <div class="copyright text-center text-sm text-muted text-lg-start">
                                ©
                                <script>
                                    document.write(new Date().getFullYear())
                                </script>
                                <a href="https://partnership.co.id" class="font-weight-bold">Partnership</a></strong>
                                All rights
                                reserved.
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <ul class="nav nav-footer justify-content-center justify-content-lg-end">
                                <p class="text-sm"><i>Your Solution Partner</i></p>
                            </ul>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </main>

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <!-- Datatables JS -->
    <script src="https://cdn.datatables.net/2.1.8/js/dataTables.js"></script>

    @stack('scripts')

    <!--   Core JS Files   -->
    <script src="{{ asset('assets/Argon/assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/Argon/assets/js/core/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/Argon/assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/Argon/assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/Argon/assets/js/plugins/chartjs.min.js') }}"></script>
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ asset('assets/Argon/assets/js/argon-dashboard.js?v=2.1.0') }}"></script>

    <!-- Sticky navbar -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            navbarFixed();
        });
    </script>

    <!-- Dark Mode -->
    <script>
        function toggleDarkSidebar(el) {
            let isDark = el.checked;
            let sidebar = document.querySelector('.sidenav'); // Sidebar utama
            let body = document.body;

            let whiteClass = "bg-white";
            let darkClass = "bg-default";

            // Perbarui sidebar background
            if (isDark) {
                sidebar.classList.remove(whiteClass);
                sidebar.classList.add(darkClass);
            } else {
                sidebar.classList.remove(darkClass);
                sidebar.classList.add(whiteClass);
            }

            // Perbarui warna teks sidebar
            let textElements = document.querySelectorAll('.sidenav .nav-link');

            textElements.forEach(el => {
                // Hanya ubah elemen yang TIDAK memiliki active bg-primary
                if (!el.classList.contains('active') || !el.classList.contains('bg-gradient-primary')) {
                    if (isDark) {
                        el.classList.remove('text-dark');
                        el.classList.add('text-white');
                    } else {
                        el.classList.remove('text-white');
                        el.classList.add('text-dark');
                    }
                }
            });

            // Perbarui logo
            let logo = document.querySelector('.navbar-brand-img');
            if (logo) {
                logo.src = isDark ?
                    "{{ asset('assets/Image/logo/Light.webp') }}" // Ganti ke logo Light
                    :
                    "{{ asset('assets/Image/logo/Dark.webp') }}"; // Kembali ke logo Dark
            }

            // Panggil fungsi darkMode untuk mengganti mode gelap
            darkMode(el);

            // Mengubah label checkbox sesuai mode
            let label = document.querySelector("label[for='toggle-mode']");
            if (label) {
                label.textContent = isDark ? "Light Mode" : "Dark Mode";
            }
        }
    </script>

    <!-- Active Class -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var url = window.location.href;

            document.querySelectorAll(".nav-link").forEach(function(link) {
                if (link.href === url || url.startsWith(link.href)) {
                    link.classList.add("active", "bg-gradient-primary", "text-white");
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
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
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
