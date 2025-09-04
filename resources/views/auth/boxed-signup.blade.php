@extends('layouts.guest')
@section('content')
    <div x-data="auth">
        <div class="position-absolute top-0 start-0 w-100 h-100">
            <img src="{{ asset('assets/images/auth/bg-gradient.png') }}" alt="image" class="h-100 w-100 object-fit-cover" />
        </div>
        <div class="position-relative d-flex min-vh-100 align-items-center justify-content-center bg-cover bg-center bg-no-repeat px-3 py-5 px-sm-4 py-sm-5" style="background-image: url(/assets/images/auth/map.png);">
            <img src="{{ asset('assets/images/auth/coming-soon-object1.png')}}" alt="image" class="position-absolute start-0 top-50 translate-middle-y h-100" style="max-height: 893px;" />
            <img src="{{ asset('assets/images/auth/coming-soon-object2.png')}}" alt="image" class="position-absolute start-0 top-0 h-100" style="left: 24px; max-height: 160px;" />
            <img src="{{ asset('assets/images/auth/coming-soon-object3.png')}}" alt="image" class="position-absolute end-0 top-0 h-100" style="max-height: 300px;" />
            <img src="{{ asset('assets/images/auth/polygon-object.svg')}}" alt="image" class="position-absolute bottom-0" style="right: 28%;" />
            <div class="position-relative w-100 rounded-3 p-2" style="max-width: 450px; background: linear-gradient(45deg,#fff9f9_0%,rgba(255,255,255,0)_25%,rgba(255,255,255,0)_75%,_#fff9f9_100%);">
                <div class="position-relative d-flex flex-column justify-content-center rounded-3 px-4 py-5 px-sm-5 py-sm-5" style="background: rgba(255,255,255,0.6); backdrop-filter: blur(16px);">
                    <div class="mx-auto w-100" style="max-width: 500px;">
                        <div class="mb-5">
                            <h1 class="display-6 fw-bold text-uppercase text-primary">Sign up</h1>
                            <p class="fs-6 fw-bold text-muted">Enter your information to sign up</p>
                        </div>
                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('signup') }}" class="d-grid gap-4">
                            @csrf
                            <div class="form-group">
                                <label for="name" class="form-label">Your Name</label>
                                <div class="position-relative">
                                    <input id="name" type="text" placeholder="Enter Your Name" class="form-control ps-5" name="name" value="{{ old('name') ? old('name') : '' }}" />
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3">
                                        <i class="fas fa-user fa-sm"></i>
                                    </span>
                                </div>
                                @error('name')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="company_name" class="form-label">Company Name</label>
                                <div class="position-relative">
                                    <input id="company_name" type="text" placeholder="Enter Company Name" class="form-control ps-5" name="company_name" value="{{ old('company_name') ? old('company_name') : '' }}" />
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3">
                                        <i class="fas fa-user fa-sm"></i>
                                    </span>
                                </div>
                                @error('company_name')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <div class="position-relative">
                                    <input id="email" type="email" placeholder="Enter Email" class="form-control ps-5" name="email" value="{{ old('email') ? old('email') : '' }}" />
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3">
                                        <i class="fas fa-user fa-sm"></i>
                                    </span>
                                </div>
                                @error('email')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="Password" class="form-label">Password</label>
                                <div class="position-relative">
                                    <input id="Password" type="password" placeholder="Enter Password" class="form-control ps-5" name="password" value="" />
                                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3">
                                        <i class="fas fa-lock fa-sm"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <div class="position-relative">
                                    <input id="password_confirmation" type="password" placeholder="Enter Confirm Password" class="form-control ps-5" name="password_confirmation" value="" />
                                </div>
                                @error('password_confirmation')
                                    <div class="alert alert-danger mt-2">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                    <input type="checkbox" id="remember" name="remember" class="form-check-input me-2">
                                    <label for="remember" class="form-check-label">Remember me</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 mt-4 fw-bold text-uppercase shadow"> Sign in </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
