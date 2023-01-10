@extends('layouts.auth')

@section('template-custom-js')
    <script src={{asset('vendor/js/custom.min.js')}}></script>
@endsection

@section('layout-content')
<section id="wrapper">
    <div class="login-register" style="background-image:url(/assets/images/background/login.jpg);">
        <div class="login-box card">
            <div class="card-body">
                <form class="form-horizontal form-material" id="loginform" method="POST" action="{{ route('login') }}">
                    @csrf

                    @if (session('error'))
                    <div class="alert alert-danger">
                            {{ session('error') }}
                    </div>
                    @endif
                    @include('common.errors')
                    @include('common.success')
    
            
                    <h3 class="box-title mb-3">Afya Call</h3>
                    <div class="form-group ">
                        <input id="login" type="text"
                        class="form-control{{ $errors->has('username') || $errors->has('email') ? ' is-invalid' : '' }}"
                        name="login" value="{{ old('username') ?: old('email') }}" required autofocus>
          
                        @if ($errors->has('username') || $errors->has('email'))
                            <span class="invalid-feedback">
                                <strong>{{ $errors->first('username') ?: $errors->first('email') }}</strong>
                            </span>
                        @endif

                    </div>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <input id="password" type="password" placeholder="Password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="checkbox checkbox-primary float-left pt-0">
                                <input id="checkbox-signup" type="checkbox" {{ old('remember') ? 'checked' : '' }}>
                                <label for="checkbox-signup"> Remember me </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group text-center mt-3">
                        <div class="col-xs-12">
                            <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Log In</button>
                        </div>
                    </div>
     
                </form>
               
            </div>
        </div>
    </div>

</section>

@endsection
