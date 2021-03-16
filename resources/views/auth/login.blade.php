{{-- @extends('adminlte::auth.login') --}}
@extends('layouts.z')

@section('title')
Login
@endsection

@push('css')
	<style>
		.login100-form-bgbtn{
			background: -webkit-linear-gradient(right, #e64c26, #f3d031, #e64c26, #f3d031);
		}
	</style>
@endpush

@section('content')
	<div class="limiter">
		<div class="container-login100" style="background-image: url({{ asset('images/bg2.jpg') }});">
			<div class="w-100">
				@include('flashAlert')
			</div>
			<div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
				<form class="login100-form validate-form" method="post" action="{{ route('login') }}">
					{{ csrf_field() }}
					<div class="text-center  p-b-30">
						<img src="{{ asset('img/logo.png') }}" style="max-width: 50%;">
					</div>
					<span class="login100-form-title p-b-30">
						Login
					</span>

					<div class="wrap-input100 validate-input m-b-23" data-validate = "Username is reauired">
						<span class="label-input100">Email</span>
						<input class="input100" type="email" name="email" placeholder="Type your email" value="{{ old('email') }}">
						<span class="focus-input100" data-symbol="&#xf206;"></span>
						@if($errors->has('email'))
			                <div class="invalid-feedback">
			                    <strong style="color: red; font-size: 11px">{{ $errors->first('email') }}</strong>
			                </div>
			            @endif
					</div>

					<div class="wrap-input100 validate-input" data-validate="Password is required">
						<span class="label-input100">Password</span>
						<input class="input100" type="password" name="password" placeholder="Type your password">
						<span class="focus-input100" data-symbol="&#xf190;"></span>
					</div>
					@if(!empty($errors->first()))
						<div class="">
							<strong style="color: red; font-size: 11px">{{ $errors->first() }}</strong>
						</div>
					@endif
					<div class="text-right p-t-8 p-b-31">
						<a href="{{ url('/password/reset') }}">
							Forgot password?
						</a>
						<br>
						<a href="{{ route('resend-email-activation') }}">
							Resend Email Activation
						</a>
					</div>
					<div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button class="login100-form-btn">
								Login
							</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection