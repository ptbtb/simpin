@extends('adminlte::page')
@section('title')
	{{ $title }}
@endsection

@section('content_header')
<h4>{{ $title }}</h4>
@endsection

@section('css')
	<style>
		.img-fit{
			object-fit: cover;
			object-position: center;
			width: 100%;
			height: 230px;
		}
	</style>
@endsection

@section('content')
<form method="post" action="{{ route('user-edit', ['id' => $user->id]) }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="card">
		<div class="card-body">
			<div class="row form-group">
				<div class="col-md-12"><label>Reset Password</label></div>
				<div class="col-md-4">
					<input type="password" name="new_password" class="form-control" autocomplete="new-password">
				</div>
				<div class="col-md-2">
					<button type="submit" class="btn btn-sm btn-success form-control" name="reset_password" value="reset_password"><i class="fa fa-save"></i> Reset Password</button>
				</div>
			</div>
		</div>
	</div>
	<div class="card">
		<div class="card-body">
			<input type="hidden" name="user_id" value="{{ $user->id }}">
			<div class="row">
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-6 form-group">
							<label>Email</label>
							<input type="email" name="email" value="{{ $user->email }}" placeholder="Your Email" class="form-control" readonly>
						</div>
						<div class="col-md-6 form-group">
							<label>Role</label>
							<select name="role_id" class="form-control" required>
								@php
									$userRole = $user->roles->first();
								@endphp
								<option value="">Choose One</option>
								@foreach ($roles as $role)
									<option value="{{ $role->id }}" {{ ($userRole && $userRole->id == $role->id)? 'selected':'' }}>{{ $role->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-6 form-group">
							<label>Name</label>
							<input type="text" name="name" value="{{ $user->name }}" placeholder="Your Name" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="form-control btn btn-sm btn-success"><i class="fa fa-save"></i> Update</button>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group text-center">
						<label>Profile Picture</label>
						<div class="form-group">
							<div class="col-md-12 text-center" id="photoButton">
								@if(isset($user) && $user->photo_profile_path)
									<img class="img-fit" id="photoPreview" src="{{ secure_asset($user->photo_profile_path) }}"/>
								@else
									<img class="img-fit" id="photoPreview" src="{{ asset('img/no_image_available.jpeg') }}">
								@endif
								<span class="btn btn-default btn-file mt-2">
									Choose Photo<input type="file" name="photo">
								</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>
@endsection

@section('js')
	<script>
		function readURL(input, previewContainer) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#' + previewContainer).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
		}
		
		$('#photoButton').on('change', '.btn-file :file', function () {
            readURL(this, 'photoPreview');
        });
	</script>
@endsection