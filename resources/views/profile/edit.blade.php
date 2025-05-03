@extends('layouts.app')

@section('title', 'Profile Settings')

@section('navbar')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="text-white opacity-5" href="javascript:;">Halaman</a></li>
            <li class="breadcrumb-item text-sm text-white active" aria-current="page">Profile Settings</li>
        </ol>
        <h6 class="nav-breadcrumb font-weight-bolder text-white mb-0">Profile Settings</h6>
    </nav>
@endsection

@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card my-4">
                    <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                        <div class="bg-gradient-primary shadow-dark border-radius-lg pt-4 pb-3">
                            <h6 class="text-white text-capitalize ps-3">Profile Settings</h6>
                        </div>
                    </div>
                    <div class="card-body table-responsive pb-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="col-lg-6">
                                            <form action="{{ route('profile.update', auth()->user()->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <p class="m-0"><strong>Profile Information</strong></p>
                                                <small>Perbarui informasi profil akun Anda.</small><br>
                                                <label class="mt-4 mb-0 form-label col-form-label-sm"
                                                    for="name">Name</label>
                                                <div class="input-group mb-3">
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        aria-describedby="name" value="{{ $user->name }}">
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-dark text-xs">SAVE</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card my-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="col-lg-6">
                                            <form action="{{ route('profile.password', auth()->user()->id) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <p class="m-0"><strong>Update Password</strong></p>
                                                <small>Pastikan akun Anda menggunakan kata sandi yang panjang dan acak untuk tetap aman.</small><br>
                                                <label class="mt-4 mb-0 form-label col-form-label-sm" for="old_password">Current
                                                    Password</label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control" id="old_password"
                                                        name="old_password" aria-describedby="old_password">
                                                </div>
                                                <label class="mb-0 form-label col-form-label-sm" for="new_password">New
                                                    Password</label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control" id="new_password"
                                                        name="new_password" aria-describedby="new_password">
                                                </div>
                                                <label class="mb-0 form-label col-form-label-sm" for="confirm_password">Confirm
                                                    Password</label>
                                                <div class="input-group mb-3">
                                                    <input type="password" class="form-control" id="confirm_password"
                                                        name="confirm_password" aria-describedby="confirm_password">
                                                </div>

                                                <button type="submit" class="btn btn-sm btn-dark text-xs">SAVE</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
@endpush

@push('scripts')
@endpush
