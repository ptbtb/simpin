{{-- @extends('adminlte::auth.passwords.reset') --}}
@extends('layouts.z')

@section('title')
Resend Email Activation
@endsection

@section('content')
	<div class="limiter">
        @include('flashAlert')
		<div class="container-login100" style="background-image: url({{ asset('images/bg-01.jpg') }});">
			<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
				<form class="login100-form validate-form" method="post" action="{{ route('resend-email-activation') }}">
                    {{ csrf_field() }}
					<span class="login100-form-title p-b-49">
						Resend Email Activation
					</span>
                    
					<div class="wrap-input100 validate-input m-b-23" data-validate = "Username is reauired">
						<span class="label-input100">Email</span>
						<input class="input100" type="email" name="email" placeholder="Type your email" value="{{ old('email') }}">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
						@if($errors->has('email'))
			                <div class="invalid-feedback">
			                    <strong style="color: red; font-size: 11px"{{ $errors->first('email') }}></strong>
			                </div>
			            @endif
                    </div>
					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button type="submit" class="login100-form-btn">
                                Resend Email Activation
                            </button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection