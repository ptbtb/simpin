{{-- @extends('adminlte::auth.passwords.reset') --}}
@extends('layouts.z')

@section('title')
Reset Password
@endsection

@section('content')
	<div class="limiter">
		<div class="container-login100" style="background-image: url({{ asset('images/bg-01.jpg') }});">
			<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
				<form class="login100-form validate-form" method="post" action="{{ url('password/reset') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="token" value="{{ $token }}">
					<span class="login100-form-title p-b-49">
						Reset Password
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
                    
                    <div class="wrap-input100 validate-input m-b-23" data-validate = "Password Required">
                        <span class="label-input100">New Password</span>
                        <input type="password" name="password" class="input100" placeholder="New Password">
                        <span class="focus-input100" data-symbol="&#xf190;"></span>
                        @if($errors->has('password'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('password') }}</strong>
                            </div>
                        @endif
                    </div>
                    
                    <div class="wrap-input100 validate-input m-b-23" data-validate = "Password Required">
                        <span class="label-input100">Confirm New Password</span>
                        <input type="password" name="password_confirmation" class="input100" placeholder="Confirm New Password">
                        <span class="focus-input100" data-symbol="&#xf190;"></span>
                        @if($errors->has('password_confirmation'))
                            <div class="invalid-feedback">
                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                            </div>
                        @endif
                    </div>
					@if(!empty($errors->first()))
                        <div class="text-center mb-3">
                            <strong style="color: red; font-size: 11px">{{ $errors->first() }}</strong>
                        </div>
                    @endif
					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button type="submit" class="login100-form-btn">
                                Reset Password
                            </button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection