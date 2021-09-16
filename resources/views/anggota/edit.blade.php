@extends('adminlte::page')

@section('title', 'Tambah Data Anggota')
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
			<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
			<li class="breadcrumb-item"><a href="{{ route('anggota-list') }}">Anggota</a></li>
			<li class="breadcrumb-item active">Edit Anggota</li>
		</ol>
	</div>
</div>
@stop
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
		<form action="{{ route('anggota-edit', ['id' => $anggota->kode_anggota]) }}" method="post" id="anggota_form" role="form" enctype="multipart/form-data">
			{{ csrf_field() }}
			<div class="row">
				<div class="col-md-8">
					<div class="card box-custom">
						<div class="card-header border-0 pb-0">
							<h5>Form Registrasi</h5>
						</div>
						<div class="card-body pt-1">
							<div class="row">
								<div class="col-md-6 form-group">
									<label>Kode Anggota</label>
									<input type="text" name="kode_anggota" class="form-control" size="54px" value="{{ $anggota->kode_anggota }}" readonly title="Kode harus diisi" />
								</div>
								<div class="col-md-6 form-group">
									<label>Unit</label>
									<select id="companyId" name="company" class="form-control">
										<option value="">Pilih Satu</option>
										@foreach ($companies as $company)
											<option value="{{ $company->id }}" {{ $company->id == $anggota->company_id ? 'selected' : ''}} >{{ $company->nama }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-md-6 form-group">
									<label>Jenis Anggota</label>
									<select id="jenisAnggota" name="jenis_anggota" class="form-control">
										<option value="">Pilih Satu</option>
										@foreach ($jenisAnggotas as $jenisAnggota)
											<option value="{{ $jenisAnggota->id_jenis_anggota }}" {{ ($jenisAnggota->id_jenis_anggota == $anggota->id_jenis_anggota)? 'selected':'' }}>{{ $jenisAnggota->nama_jenis_anggota }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-md-6 form-group">
									<label>Kelas Unit</label>
									<select id="kelasCompany" name="kelas_company" class="form-control" disabled>
										<option value="">Pilih Satu</option>
										@if($kelasCompany != "")
											@foreach ($kelasCompany as $listKelasCompany)
												<option value="{{ $listKelasCompany->id }}" {{ $listKelasCompany->id == $anggota->kelas_company_id ? 'selected' : ''}}>{{ $listKelasCompany->nama }}</option>
											@endforeach
										@endif
									</select>
								</div>
								<div class="col-md-6 form-group">
									<label>NIPP</label>
									<input type="text" name="nipp" size="54" class="form-control" value="{{ $anggota->nipp }}" placeholder="NIPP"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Nama Lengkap</label>
									<input type="text" name="nama_anggota" class="form-control" size="54" class="required" title="Nama harus diisi" value="{{ $anggota->nama_anggota }}"  placeholder="Nama Anggota" />
								</div>
								<div class="col-md-6 form-group">
									<label>Tempat Lahir</label>
									<input type="text" name="tmp_lahir" size="54" class="form-control" value="{{ $anggota->tempat_lahir }}" placeholder="Tempat Lahir"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Tanggal Lahir</label>
									<input type="date" class="form-control" name="tgl_lahir" class="required" title="Tanggal Lahir harus diisi" value={{ $anggota->tgl_lahir }} placeholder="Tanggal Lahir">
								</div>
								<div class="col-md-6 form-group">
									<label>Jenis Kelamin</label>
									<select name="jenis_kelamin" class="form-control">
                      					<option value="">Pilih Satu</option>
										<option value="L" {{ ($anggota->jenis_kelamin == 'L')? 'selected':'' }}>Laki - laki</option>
										<option value="P" {{ ($anggota->jenis_kelamin == 'P')? 'selected':'' }}>Perempuan</option>
									</select>
								</div>
								<div class="col-md-6 form-group">
									<label>Alamat Anggota</label>
									<input type="text" name="alamat_anggota" class="form-control" id="alamat_anggota" rows="5" cols="41" class="required" title="Alamat harus diisi" value="{{ $anggota->alamat_anggota }}" placeholder="Alamat Anggota"/>
								</div>
								<div class="col-md-6 form-group">
									<label>KTP</label>
									<input type="text" name="ktp" size="54" class="form-control" value="{{ $anggota->ktp }}" placeholder="KTP"/>
								</div>
								
								<div class="col-md-6 form-group">
									<label>Tanggal Masuk</label>
									<input type="date" name="tgl_masuk" class="form-control" value={{ $anggota->tgl_masuk }} placeholder="Tanggal Masuk" >
								</div>
								<div class="col-md-6 form-group">
									<label>Email</label>
									<input type="email" name="email" size="54" class="form-control" value="{{ $anggota->email }}" placeholder="Email"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Telepon</label>
									<input type="text" name="telp" size="54" class="form-control" value="{{ $anggota->telp }}" placeholder="Telepon"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Emergency Kontak</label>
									<input type="text" name="emergency_kontak" size="54" value="{{ $anggota->emergency_kontak }}" class="form-control" placeholder="Emergency Kontak"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Nomer Rekening</label>
									<input type="text" name="no_rek" class="form-control" value="{{ $anggota->no_rek }}"  placeholder="Nomor Rekening Mandiri"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Bank</label>
									{!! Form::select('bank', $bank,$anggota->id_bank, ['class' => 'form-control bank', 'placeholder' => 'Nama Bank']) !!}
								</div>
								@foreach ($listJenisPenghasilan as $jenisPenghasilan)
									<div class="col-md-6 form-group">
										<label>{{ $jenisPenghasilan->name }}</label>
										<input type="text" id="penghasilanTertentu{{ $jenisPenghasilan->id }}" name="penghasilan[{{ $jenisPenghasilan->id }}]" class="form-control toRupiah" placeholder="{{ $jenisPenghasilan->name }}"
										@if($listPenghasilan)
										@if (!empty($listPenghasilan->where('id_jenis_penghasilan', $jenisPenghasilan->id)->first()))
											value="{{ $listPenghasilan->where('id_jenis_penghasilan', $jenisPenghasilan->id)->first()->value }}"
										@endif
										@endif
										
										onkeypress="return isNumberKey(event)">
									</div>
									<div class="col-md-6 form-group">
										<label>Dokumen {{ $jenisPenghasilan->name }}</label>
										<div class="custom-file">
										<input type="file" class="custom-file-input"  id="file_penghasilanTertentu{{ $jenisPenghasilan->id }}" name="file_penghasilan[{{ $jenisPenghasilan->id }}]"  accept="application/pdf" style="cursor: pointer">
											@if($listPenghasilan)
											@if (!empty($listPenghasilan->where('id_jenis_penghasilan', $jenisPenghasilan->id)->first()))
												<label class="custom-file-label" for="customFile">{{ $listPenghasilan->where('id_jenis_penghasilan', $jenisPenghasilan->id)->first()->file_path }}</label>
											
											@endif
											@else
												<label class="custom-file-label" for="customFile">Choose Document</label>
											@endif
										</div>
									</div>
								@endforeach
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card box-custom">
						<div class="card-body pt-1">
							<div class="form-group text-center">
								<label>KTP Photo</label>
								<div class="form-group">
									<div class="text-center" id="photoKtpButton">
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
						<hr>
						<div class="card-header border-0 pb-0">
							<h5>Dibuat Oleh</h5>
						</div>
						<div class="card-body pt-1">
							<div class="form-group">
								<label>User Entri</label>
								<input type="text"  class="form-control" name="u_entry" size="54" value="{{ Auth::user()->name }}" readonly >
							</div>
							<div class="form-group">
								<label>Tanggal Entri</label>
								<input type="text" name="tgl_entri" class="form-control" size="54" value="<?php echo date("Y-m-d"); ?>" readonly/>
							</div>
							<div class="form-group">
								<button class="btn btn-sm btn-success form-control"><span class='fa fa-save'></span> Simpan</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@stop

@section('css')
@stop

@section('js')
	<script src="{{ asset('js/collect.min.js') }}"></script>
	<script src="{{ asset('vendor/jquery-validation/jquery.validate.js') }}"></script>
<script>
	var companyId = $('#companyId').val();
	var jenisAnggotaId = $('#jenisAnggota').val();
	var listKelasCompany = collect({!!$kelasCompany!!});

    $(document).ready(function () {
//        $.validator.setDefaults({
//            submitHandler: function () {
//                alert("Form  submitted!");
//            }
//        });
		initiateSelect2();
		if($('#kelasCompany').val()){
			$('#kelasCompany').prop('disabled', false);
		}
        $('#anggota_form').validate({
			rules: {
			
			ktp: {
				required: true
			},
			nipp: {
				required: true
			},
			
			no_rek: {
				required: true
			},
			nama_anggota: {
				required: true
			},
			tgl_lahir: {
				required: true
			},
			emergency_kontak: {
				required: true
			},
			telp: {
				required: true
			},
			jenis_kelamin: {
				required: true
			},

			},
			messages: {
			
			ktp: {
				required: "Wajib di isi"
			},
			nipp: {
				required: "Wajib di isi"
			},
			
			no_rek: {
				required: "Wajib di isi"
			},
			nama_anggota: {
				required: "Wajib di isi"
			},
			tgl_lahir: {
				required: "Wajib di isi"
			},
			emergency_kontak: {
				required: "Wajib di isi"
			},
			telp: {
				required: "Wajib di isi"
			},
			},
			errorElement: 'span',
			errorPlacement: function (error, element) {
			error.addClass('invalid-feedback');
			element.closest('.form-group').append(error);
			},
			highlight: function (element, errorClass, validClass) {
			$(element).addClass('is-invalid');
			},
			unhighlight: function (element, errorClass, validClass) {
			$(element).removeClass('is-invalid');
			}
		});

		$('#photoKtpButton').on('change', '.btn-file :file', function () {
			readURL(this, 'ktpPreview');
		});

		$(".custom-file-input").on("change", function() {
			var fileName = $(this).val().split("\\").pop();
			$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
		});

		$('.toRupiah').each(function (index)
		{
			var value = $(this).val();
			if (value != null && value != '')
			{
				$(this).val(toRupiah(value));
			}
		});

		$('.toRupiah').on('keyup', function () {
			var val = $(this).val();
			val = val.replace(/[^\d]/g, "",'');
			$(this).val(toRupiah(val));
		});
    });
    var sel = $("#jenisAnggota").val();

    if(sel==3){
        $("#penghasilanTertentu4").prop('required',false);
    }



	$('#companyId').on('change', function(){
		if($(this).children("option:selected").val() != companyId){
			companyId = $(this).children("option:selected").val();
			$('#kelasCompany').val('');
			getKelasCompany(companyId, jenisAnggotaId);
		}
	})

	if (companyId!=""){
	$('#kelasCompany').val('');
			getKelasCompany(companyId, jenisAnggotaId);	
	}

	$('#jenisAnggota').on('change', function(){
		if($(this).children("option:selected").val() != jenisAnggotaId){
			jenisAnggotaId = $(this).children("option:selected").val();

            if(jenisAnggotaId==3){
                $("#penghasilanTertentu4").prop('required',false);
            }else{
                $("#penghasilanTertentu4").prop('required',true);
            }
			$('#kelasCompany').val('');
			getKelasCompany(companyId, jenisAnggotaId);
		}
	})

	function initiateSelect2()
	{
		 $("#kelasCompany").select2({
            ajax: {
                url: '{{ route('anggota-ajax-getKelasCompany') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                     	companyId : companyId,
						jenisAnggotaId : jenisAnggotaId
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

	function getKelasCompany(companyId, jenisAnggotaId){
		return $.ajax({
            url: '{{ route('anggota-ajax-getKelasCompany') }}',
            dataType: 'json',
            data: {
                'companyId' : companyId,
				'jenisAnggotaId' : jenisAnggotaId
            },
            success: function (response) {
				if(response.length !== 0){
					$('#kelasCompany').prop('disabled', false);
					$('#kelasCompany').val(response);
					$('#kelasCompany').val("").change();
				} else {
					$('#kelasCompany').prop('disabled', true);
					$('#kelasCompany').val("").change();

				}
            }
        })
	}

	function readURL(input, previewContainer, previewDocument) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#' + previewContainer).attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
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
@stop
