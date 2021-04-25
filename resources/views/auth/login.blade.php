@extends('layouts.app_unauthorized')

@section('content')
    <div class="container text-center mt-5 pt-3" style="background: rgba(234,234,234,0.50); max-width: 450px;">
        <img src="{{ asset('/img/logo/logo_ff.png') }}" width="180px">
        <h3 class="text-center w-100 mt-5">Login</h3>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="card-body">
                <div class="form-group text-left">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>

                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email" autofocus autocomplete="off">
                    </div>

                    @error('email')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="form-group text-left">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>

                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password">
                    </div>
                    @error('password')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-dark w-100">{{ __('Login') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('message')
    @error('email')
    <script type="text/javascript">
        Swal.fire({
            icon: 'error',
            button: false,
            text: 'Username/Password Salah!',
        })
    </script>
    @enderror
@endsection
