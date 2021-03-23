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
							<div class="col-md-6 form-group">
								<label>Company</label>
								<input type="text" name="company" class="form-control" value="@if($anggota->company) {{ $anggota->company->nama }} @else - @endif" readonly>
							</div>
							
							@if (!$classList == '')
							<div class="col-md-6 form-group">
								<label>Company Class</label>
								@if (\Auth::user()->isAnggota())
									@if ($anggota && $anggota->kelas_company_id)
										<input type="text" name="kelas_company_name" class="form-control" value="{{ $anggota->kelasCompany->nama }}" readonly>
										<input type="hidden" name="kelas_company" class="form-control" value="{{ $anggota->kelasCompany->id }}" readonly>
									@else
										<select name="kelas_company" class="form-control">
											<option value="">Choose One</option>
											@foreach($classList as $itemClass)
												<option value="{{ $itemClass->id }}" {{ ($anggota && $anggota->kelas_company_id == $itemClass->id)? 'selected':'' }}>{{ $itemClass->nama }}</option>
											@endforeach
										</select>
									@endif
								@else
									<select name="kelas_company" class="form-control">
										<option value="">Choose One</option>
										@foreach($classList as $itemClass)
											<option value="{{ $itemClass->id }}" {{ ($penghasilan && $penghasilan->kelas_company_id == $itemClass->id)? 'selected':'' }}>{{ $itemClass->nama }}</option>
										@endforeach
									</select>
								@endif
							</div>
							@endif
                                                        <div class="col-md-6 form-group">
								
							</div>
							@foreach ($listJenisPenghasilan as $jenisPenghasilan)
								<div class="col-md-6 form-group">
									<label>{{ $jenisPenghasilan->name }}</label>
									<input type="text" id="penghasilanTertentu{{ $jenisPenghasilan->id }}" name="penghasilan[{{ $jenisPenghasilan->id }}]" class="form-control toRupiah" placeholder="{{ $jenisPenghasilan->name }}" 
									@if ($listPenghasilan)
										value="{{ $listPenghasilan->where('id_jenis_penghasilan', $jenisPenghasilan->id)->first()->value }}"
									@endif 
									onkeypress="return isNumberKey(event)">
								</div>
								<div class="col-md-6 form-group">
									<label>Dokumen {{ $jenisPenghasilan->name }}</label>
									<div class="custom-file">
									<input type="file" class="custom-file-input"  id="file_penghasilanTertentu{{ $jenisPenghasilan->id }}" name="file_penghasilan[{{ $jenisPenghasilan->id }}]"  accept="application/pdf" style="cursor: pointer">
										@if($listPenghasilan && $listPenghasilan->where('id_jenis_penghasilan', $jenisPenghasilan->id)->first()->file_path)
											<label class="custom-file-label" for="customFile">{{ $listPenghasilan->where('id_jenis_penghasilan', $jenisPenghasilan->id)->first()->file_path }}</label>
										@else
											<label class="custom-file-label" for="customFile">Choose Document</label>
										@endif
									</div>
								</div>
							@endforeach
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
										<img class="img-fit" id="photoPreview" src="{{ asset($user->photo_profile_path) }}"/>
									@else
										<img class="img-fit" id="photoPreview" src="{{ asset('img/no_image_available.jpeg') }}">
									@endif
									<span class="btn btn-default btn-file mt-2">
										Choose Photo<input type="file" name="photo" accept="image/*">
									</span>
								</div>
							</div>
						</div>
						<div class="form-group text-center">
							<label>KTP Photo</label>
							<div class="form-group">
								<div class="col-md-12 text-center" id="photoKtpButton">
									@if(isset($anggota) && $anggota->foto_ktp)
										<img class="img-fit" id="ktpPreview" src="{{ asset($anggota->foto_ktp) }}"/>
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

		$(document).ready(function ()
		{
			$('.toRupiah').each(function (index)
			{
				var value = $(this).val();
				if (value != null && value != '')
				{
					$(this).val(toRupiah(value));
				}
			});
			initiateEvent();
		});

		function initiateEvent()
		{
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

			$('.toRupiah').on('keyup', function () {
				var val = $(this).val();
				val = val.replace(/[^\d]/g, "",'');
				$(this).val(toRupiah(val));
			});
		}
		
		function toRupiah(number)
        {
            var stringNumber = number.toString();
            var length = stringNumber.length;
            var temp = length;
            var res = "Rp ";
            for (let i = 0; i < length; i++) {
                res = res + stringNumber.charAt(i);
                temp--;
                if (temp%3 == 0 && temp > 0)
                {
                    res = res + ".";
                }
            }
            return res;
        }

		function isNumberKey(evt)
		{
			var charCode = (evt.which) ? evt.which : event.keyCode
			if (charCode > 31 && (charCode < 48 || charCode > 57))
			return false;

			return true;
		}
	</script>
@endsection