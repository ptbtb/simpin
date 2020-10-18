@extends('adminlte::page')

@section('title', 'Tambah Data Anggota')
@section('plugins.Datatables', true)
@section('content_header')
<h1>Tambah Data Anggota</h1>
@stop

@section('content')
<div class="col-lg-12">
    <div class="form-panel" style="width:100%;">
        <form action="/anggota/store" method="post" id="anggota_form" role="form" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Kode Anggota</label>
                        <input type="text" name="kode_anggota" class="form-control" size="54px" value="{{$nomer}}" readonly title="Kode harus diisi" />
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Tanggal Masuk</label>
                        <input type="text" name="tgl_masuk" class="form-control" value="<?php echo date("Y-m-d"); ?>" >
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>KTP</label>
                        <input type="text" name="ktp" size="54" class="form-control"/>
                    </div>
                </div>

            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <input type="radio" name="jenis_kelamin" value="L" class="required" title="Jenis Kelamin harus diisi"/> Laki-laki
                <input type="radio" name="jenis_kelamin" value="P" class="required" title="Jenis Kelamin harus diisi"/> Perempuan
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_anggota" class="form-control" size="54" class="required" title="Nama harus diisi" />
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Tempat Lahir</label>
                        <input type="text" name="tmp_lahir" size="54" class="form-control"/>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tgl_lahir" class="required" title="Tanggal Lahir harus diisi">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Alamat Anggota</label>
                        <input type="text" name="alamat_anggota" class="form-control" id="alamat_anggota" rows="5" cols="41" class="required" title="Alamat harus diisi"/>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Telepon</label>
                        <input type="text" name="telp" size="54" class="form-control"/>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Lokasi Kerja</label>
                        <input type="text" name="lokasi_kerja" size="54" class="form-control"/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>NIPP</label>
                        <input type="text" name="nipp" size="54" class="form-control"/>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Nomer Rekening Mandiri</label>
                        <input type="text" name="no_rek" size="54" class="form-control"/>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>email</label>
                        <input type="text" name="email" size="54" class="form-control"/>
                    </div>  
                </div>
            </div>

            <!--            <div class="form-group">
                            <label>Simpanan Pokok</label>
                            <input type="text" name="simpanan_pokok" class="form-control" size="54" id="simpanan_pokok" class="required" readonly="" value="<?php // echo $data['besar_simpanan'];              ?>">
                        </div>-->


            <div class="row">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Emergency Kontak</label>
                        <input type="text" name="emergency_kontak" size="54" class="form-control"/>
                    </div>  
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>User Entri</label>
                        <input type="text"  class="form-control" name="u_entry" size="54" value="{{ Auth::user()->name }}" readonly >
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-group">
                        <label>Tanggal Entri</label>
                        <input type="text" name="tgl_entri" class="form-control" size="54" value="<?php echo date("Y-m-d"); ?>" readonly/>
                    </div>
                </div>
            </div>

            <button class="btn btn-info"><span class='glyphicon glyphicon-ok'></span> Simpan</button>
        </form>
    </div></div>
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