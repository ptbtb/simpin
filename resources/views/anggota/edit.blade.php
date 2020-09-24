@extends('adminlte::page')

@section('title', 'Tambah Data Anggota')
@section('plugins.Datatables', true)
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

@section('content')
<div class="card">
	<div class="card-body">
		<form action="{{ route('anggota-edit', ['id' => $anggota->kode_anggota]) }}" method="post" id="anggota_form" role="form" enctype="multipart/form-data">
			@csrf
			<div class="row">
				<div class="col-md-8">
					<div class="card box-custom">
						<div class="card-header border-0 pb-0">
							<h5>Form Registrasi</h5>
						</div>
						<div class="card-body pt-1">
							<div class="row">
								{{-- <div class="col-md-6"> --}}
									<div class="col-md-12 form-group">
										<label>Kode Anggota</label>
										<input type="text" name="kode_anggota" class="form-control" size="54px" value="{{ $anggota->kode_anggota }}" readonly title="Kode harus diisi" />
									</div>
									<div class="col-md-6 form-group">
										<label>Jenis Anggota</label>
										<select name="jenis_anggota" class="form-control">
											<option value="">Pilih Satu</option>
											@foreach ($jenisAnggotas as $jenisAnggota)
												<option value="{{ $jenisAnggota->id_jenis_anggota }}" {{ ($jenisAnggota->id_jenis_anggota == $anggota->id_jenis_anggota)? 'selected':'' }}>{{ $jenisAnggota->nama_jenis_anggota }}</option>
											@endforeach
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
										<input type="date" class="form-control" name="tgl_lahir" class="required" title="Tanggal Lahir harus diisi" value="{{ $anggota->tgl_lahir }}" placeholder="Tanggla Lahir">
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
								{{-- </div> --}}
								{{-- <div class="col-md-6"> --}}
									<div class="col-md-6 form-group">
										<label>KTP</label>
										<input type="text" name="ktp" size="54" class="form-control" value="{{ $anggota->ktp }}" placeholder="KTP"/>
									</div>
									<div class="col-md-6 form-group">
										<label>Lokasi Kerja</label>
										<input type="text" name="lokasi_kerja" size="54" value="{{ $anggota->lokasi_kerja }}" class="form-control" placeholder="Lokasi Kerja"/>
									</div>
									<div class="col-md-6 form-group">
										<label>Tanggal Masuk</label>
										<input type="text" name="tgl_masuk" class="form-control" value="{{ $anggota->tgl_masuk }}" placeholder="Tanggal Masuk" >
									</div>
									<div class="col-md-6 form-group">
										<label>email</label>
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
										<label>Nomer Rekening Mandiri</label>
										<input type="text" name="no_rek" size="54" class="form-control" value="{{ $anggota->no_rek }}"  placeholder="Nomor Rekening Mandiri"/>
									</div>
								{{-- </div> --}}
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
 <script src="{{ asset('vendor/jquery-validation/jquery.validate.js') }}"></script>
<script>
    $(document).ready(function () {
//        $.validator.setDefaults({
//            submitHandler: function () {
//                alert("Form  submitted!");
//            }
//        });
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
</script>
@stop