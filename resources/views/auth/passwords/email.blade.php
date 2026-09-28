@extends('layouts.master-without-nav')
@section('title')
    Reset Password
@endsection
@section('content')
    <div class="account-pages my-5  pt-sm-5">
        <div class="container">
            <div class="row justify-content-center">

                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div>
                        <a href="{{ url('index') }}" class="mb-5 d-block auth-logo">
                            <img src="{{ URL::asset('/assets/images/logo-eciatto.png') }}" alt="" height="22"
                                class="logo logo-eciatto">
                            <!-- <img src="{{ URL::asset('/assets/images/logo-eciatto.png') }}" alt="" height="22"
                                class="logo logo-eciatto"> -->
                        </a>
                        <div class="card">

                            <div class="card-body p-4">

                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Reset Password</h5>
                                </div>
                                <div class="p-2 mt-4">
                                    @if (session('status'))
                                        <div class="alert alert-success mb-4" role="alert">
                                            {{ session('status') }}
                                        </div>
                                    @endif
                                    <form method="POST" action="{{ route('password.email') }}">
                                        @csrf

                                        <div class="mb-3">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="text" class="form-control @error('mobile') is-invalid @enderror"
                                                name="email" value="{{ old('email') }}" id="email"
                                                placeholder="Enter email">
                                            @error('mobile')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>

                                        <div class="mt-3 text-end">
                                            <button class="btn btn-primary w-sm waves-effect waves-light"
                                                type="submit">Reset</button>
                                        </div>


                                        <div class="mt-4 text-center">
                                            <p class="mb-0">Remember It ? <a href="{{ url('login') }}"
                                                    class="fw-medium text-primary"> Signin </a></p>
                                        </div>
                                    </form>
                                </div>

                            </div>
                        </div>
                        <div class="mt-5 text-center">
                            <p>© <script>
                                    document.write(new Date().getFullYear())

                                </script> Eciatto Sdn. Bhd.</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
@endsection
