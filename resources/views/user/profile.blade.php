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
	<div class="card">
		<div class="card-body">
			<form method="post" action="{{ route('user-profile') }}" enctype="multipart/form-data">
				{{ csrf_field() }}
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
								<input type="text" name="role_name" value="{{ ($user->roles->first())? $user->roles->first()->name:'' }}" placeholder="Your Role" readonly class="form-control">
								<input type="hidden" name="role_id" value="{{ ($user->roles->first())? $user->roles->first()->id:'' }}">
							</div>
							<div class="col-md-6 form-group">
								<label>Name</label>
								<input type="text" name="name" value="{{ $user->name }}" placeholder="Your Name" class="form-control">
							</div>
							@if (!$classList == '')
							<div class="col-md-6 form-group">
								<label>Company Class</label>
								<select name="kelas_company" class="form-control">
									@foreach($classList as $itemClass)
										<option value="{{ $itemClass->id }}" {{ ($penghasilan && $penghasilan->kelas_company_id == $itemClass->id)? 'selected':'' }}>{{ $itemClass->nama }}</option>
									@endforeach
								</select>
							</div>
							@endif
							<div class="col-md-6 form-group">
								<label>Salary</label>
								<input type="number" name="salary" value="{{ ($penghasilan->gaji_bulanan)? $penghasilan->gaji_bulanan:'' }}" placeholder="Your Salary" class="form-control">
							</div>
							<div class="col-md-6 form-group">
								<label>Salary Slip</label>
								<div class="custom-file">
									<input type="file" class="custom-file-input" id="salary_slip" name="salary_slip"  accept="application/pdf">
									@if($penghasilan && $penghasilan->slip_gaji)
										<label class="custom-file-label" for="customFile">{{ $penghasilan->slip_gaji }}</label>
									@else
										<label class="custom-file-label" for="customFile">Choose Document</label>
									@endif
								</div>
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
										Choose Photo<input type="file" name="photo" accept="image/*">
									</span>
								</div>
							</div>
						</div>
						<hr>
						<div class="form-group text-center">
							<label>KTP Photo</label>
							<div class="form-group">
								<div class="col-md-12 text-center" id="photoKtpButton">
									@if(isset($penghasilan) && $penghasilan->foto_ktp)
										<img class="img-fit" id="ktpPreview" src="{{ asset($penghasilan->foto_ktp) }}"/>
									@else
										<img class="img-fit" id="ktpPreview" src="{{ asset('img/no_image_available.jpeg') }}">
									@endif
									<span class="btn btn-default btn-file mt-2">
										Choose Photo<input type="file" name="ktp_photo" accept="image/*">
									</span>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection

@section('js')
	<script>
		function readURL(input, previewContainer, previewDocument) {
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
		$('#photoKtpButton').on('change', '.btn-file :file', function () {
            readURL(this, 'ktpPreview');
        });

		$(".custom-file-input").on("change", function() {
			var fileName = $(this).val().split("\\").pop();
			$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
		});
	</script>
@endsection