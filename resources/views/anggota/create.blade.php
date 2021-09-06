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
			<li class="breadcrumb-item active">Tambah Anggota</li>
		</ol>
	</div>
</div>
@stop

@section('content')
<div class="card">
	<div class="card-body">
		<form action="{{ route('anggota-create') }}" method="post" id="anggota_form" role="form" enctype="multipart/form-data">
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
									<input type="number" value="{{$nomer}}" name="kode_anggota" class="form-control" size="54px" placeholder="Kode Anggota" title="Kode harus diisi" readonly />
								</div>
								<div class="col-md-6 form-group">
									<label>Unit</label>
									<select id="companyId" name="company" class="form-control">
										<option value="">Pilih Satu</option>
										@foreach ($companies as $company)
											<option value="{{ $company->id }}">{{ $company->nama }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-md-6 form-group">
									<label>Jenis Anggota</label>
									<select id="jenisAnggota" name="jenis_anggota" class="form-control">
										<option value="">Pilih Satu</option>
										@foreach ($jenisAnggotas as $jenisAnggota)
											<option value="{{ $jenisAnggota->id_jenis_anggota }}">{{ $jenisAnggota->nama_jenis_anggota }}</option>
										@endforeach
									</select>
								</div>
								<div class="col-md-6 form-group">
									<label>Kelas Unit</label>
									<select id="kelasCompany" name="kelas_company" class="form-control" disabled>
										<option value="">Pilih Satu</option>
										@if($kelasCompany != "")
											@foreach ($kelasCompany as $listKelasCompany)
												<option value="{{ $listKelasCompany->id }}">{{ $listKelasCompany->nama }}</option>
											@endforeach
										@endif
									</select>
								</div>
								<div class="col-md-6 form-group">
									<label>NIPP</label>
									<input type="text" name="nipp" size="54" class="form-control" placeholder="NIPP"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Nama Lengkap</label>
									<input type="text" name="nama_anggota" class="form-control" size="54" class="required" title="Nama harus diisi"  placeholder="Nama Anggota" />
								</div>
								<div class="col-md-6 form-group">
									<label>Tempat Lahir</label>
									<input type="text" name="tmp_lahir" size="54" class="form-control" placeholder="Tempat Lahir"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Tanggal Lahir</label>
									<input type="date" class="form-control" name="tgl_lahir" class="required" title="Tanggal Lahir harus diisi" placeholder="Tanggla Lahir">
								</div>
								<div class="col-md-6 form-group">
									<label>Jenis Kelamin</label>
									<select name="jenis_kelamin" class="form-control">
										<option value="">Pilih Satu</option>
										<option value="L">Laki - laki</option>
										<option value="P">Perempuan</option>
									</select>
								</div>
								<div class="col-md-6 form-group">
									<label>Alamat Anggota</label>
									<input type="text" name="alamat_anggota" class="form-control" id="alamat_anggota" rows="5" cols="41" class="required" title="Alamat harus diisi" placeholder="Alamat Anggota"/>
								</div>
								<div class="col-md-6 form-group">
									<label>KTP</label>
									<input type="text" name="ktp" size="54" class="form-control" placeholder="KTP"/>
								</div>
								
								<div class="col-md-6 form-group">
									<label>Tanggal Masuk</label>
									<input type="date" name="tgl_masuk" class="form-control" value="<?php echo date("Y-m-d"); ?>" placeholder="Tanggal Masuk" >
								</div>
								<div class="col-md-6 form-group">
									<label>Email</label>
									<input type="email" name="email" size="54" class="form-control" placeholder="Email"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Telepon</label>
									<input type="text" name="telp" size="54" class="form-control" placeholder="Telepon"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Emergency Kontak</label>
									<input type="text" name="emergency_kontak" size="54" class="form-control" placeholder="Emergency Kontak"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Nomer Rekening</label>
									<input type="text" name="no_rek" class="form-control"  placeholder="Nomor Rekening Mandiri"/>
								</div>
								<div class="col-md-6 form-group">
									<label>Bank</label>
									{!! Form::select('bank', $bank,'', ['class' => 'form-control bank', 'placeholder' => 'Semua']) !!}
								</div>
								<div class="col-md-6 form-group">
									<label>Password</label>
									<input type="password" name="password" placeholder="Your Password" class="form-control" value="{{ uniqid() }}" autocomplete="off" readonly>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card box-custom">
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

	var companyId;
	var jenisAnggotaId;
	var listKelasCompany = collect({!!$kelasCompany!!});
    
	$(document).ready(function () {
//        $.validator.setDefaults({
//            submitHandler: function () {
//                alert("Form  submitted!");
//            }
//        });
        initiateSelect2();
        $('#anggota_form').validate({
			rules: {
			email: {
				required: true,
				email: true,
			},
			ktp: {
				required: true
			},
			nipp: {
				required: true
			},
			lokasi_kerja: {
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
			email: {
				required: "Wajib di isi",
				email: "isi dengan email yg valid"
			},
			ktp: {
				required: "Wajib di isi"
			},
			nipp: {
				required: "Wajib di isi"
			},
			lokasi_kerja: {
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
    });

	$('#companyId').on('change', function(){
		if($(this).children("option:selected").val() != companyId){
			companyId = $(this).children("option:selected").val();
			$('#kelasCompany').val('');
			getKelasCompany(companyId, jenisAnggotaId);
		}
	})
	$('#jenisAnggota').on('change', function(){
		if($(this).children("option:selected").val() != jenisAnggotaId){
			jenisAnggotaId = $(this).children("option:selected").val();
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
</script>
@stop