{{-- @extends('adminlte::auth.passwords.email') --}}
@extends('layouts.z')

@section('title')
Reset Password
@endsection

@section('content')
	<div class="limiter">
		<div class="container-login100" style="background-image: url({{ asset('images/bg-01.jpg') }});">
			<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
				<form class="login100-form validate-form" method="post" action="{{ url('/password/email') }}">
					{{ csrf_field() }}
					<span class="login100-form-title p-b-49">
						Reset Password
					</span>

					<div class="wrap-input100 validate-input m-b-23" data-validate = "Username is reauired">
						<span class="label-input100">Email</span>
						<input class="input100" type="email" name="email" placeholder="Type your email" value="{{ old('email') }}">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
						@if($errors->has('email'))
			                <div class="invalid-feedback">
			                    <strong style="color: red; font-size: 11px"{{ $errors->first('email') }}</strong>
			                </div>
			            @endif
					</div>
					
					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn">
								Send Password Reset Link
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection