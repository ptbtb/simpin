@extends('adminlte::page')
@section('title')
	{{ $title }}
@endsection

@section('plugins.Select2', true)

@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
			<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
			<li class="breadcrumb-item"><a href="{{ route('user-list') }}">User</a></li>
			<li class="breadcrumb-item active">Create</li>
		</ol>
	</div>
</div>
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
<form method="post" action="{{ route('user-create') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-8">
					<div class="row">
						<div class="col-md-6 form-group">
							<label>Email</label>
							<input type="email" name="email" placeholder="Your Email" class="form-control" autocomplete="off" required>
						</div>
						<div class="col-md-6 form-group">
							<label>Password</label>
							<input type="password" name="password" placeholder="Your Password" class="form-control" autocomplete="off" required>
						</div>
						<div class="col-md-6 form-group">
							<label>Role</label>
							<select name="role_id" class="form-control" required id="roleOption">
								<option value="">Choose One</option>
								@foreach ($roles as $role)
									<option value="{{ $role->id }}">{{ $role->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-6 form-group" id="name">
							<label>Name</label>
							<input type="text" id="nameField" name="name" placeholder="Your Name" class="form-control" required autocomplete="off">
						</div>
						<div class="col-md-6 form-group d-none" id="anggotaSelect2">
							<label for="anggotaName">Anggota Name</label>
							<select name="kode_anggota" id="anggotaName" class="form-control">
							</select>
						</div>
						<div class="col-12 form-group mt-2 d-none" id="anggotaForm">
							<label for="detailAnggota">Detail Anggota</label>
							<div id="detailAnggota" style="background-color: #f2f2f2"></div>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="form-control btn btn-sm btn-success"><i class="fa fa-save"></i> Create User</button>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group text-center">
						<label>Profile Picture</label>
						<div class="form-group">
							<div class="col-md-12 text-center" id="photoButton">
								<img class="img-fit" id="photoPreview" src="{{ asset('img/no_image_available.jpeg') }}">
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

		$('#roleOption').on('change', function () {
			var selectedRole = $(this).children("option:selected").val();
			var roleAnggota = {{ ROLE_ANGGOTA }};
			if (selectedRole == roleAnggota)
			{
				$('#anggotaSelect2').removeClass('d-none');
				$('#anggotaForm').removeClass('d-none');
				$('#name').addClass('d-none');
				$('#nameField').prop('required',false);
				$('#anggotaName').prop('required',true);
				$("#anggotaName").select2({
					ajax: {
						url: '{{ route('api-anggota-search') }}',
						dataType: 'json',
						delay: 250,
						data: function (params) {
							var query = {
								search: params.term,
								type: 'public'
							}
							return query;
						},
						processResults: function (data) {
							return {
								results: data
							};
						}
					}
				});
			}
			else
			{
				$('#anggotaSelect2').addClass('d-none');
				$('#anggotaForm').addClass('d-none');
				$('#name').removeClass('d-none');
				$('#nameField').prop('required',true);
				$('#anggotaName').prop('required',false);
			}
		});

		$('#anggotaName').on('change', function ()
		{
			var selectedValue = $(this).children("option:selected").val();
			var baseURL = {!! json_encode(url('/')) !!};
			$.get(baseURL + "/anggota/ajax-detail/" + selectedValue, function( data ) {
				$('#detailAnggota').html(data);
			});
		});
		
	</script>
@endsection