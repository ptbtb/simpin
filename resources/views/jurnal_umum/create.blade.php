@extends('adminlte::page')

@section('plugins.Select2', true)

@section('title')
{{ $title }}
@endsection

@section('content_header')
<div class="row">
    <div class="col-6"><h4>{{ $title }}</h4></div>
    <div class="col-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('jurnal-umum-list') }}">Jurnal Umum</a></li>
            <li class="breadcrumb-item active">Tambah Jurnal Umum</li>
        </ol>
    </div>
</div>
@endsection

@section('css')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('jurnal-umum-create') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row" id="form1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tgl_transaksi">Tgl Transaksi</label>
                        <input id="tgl_transaksi" type="date" name="tgl_transaksi" class="form-control" placeholder="yyyy-mm-dd" required>
                    </div>
                    <div class="form-group">
                        <label for="nominal1">Deskripsi</label>
                        <input type="text" maxlength="255" name="deskripsi" id="deskripsi" class="form-control" placeholder="Deskripsi" autocomplete="off" required>
                    </div>
                    <div class="form-group">
                        <label>Lampiran</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="lampiran" name="lampiran" accept="application/pdf" style="cursor: pointer" required>
                            <label class="custom-file-label" for="customFile">Choose Document</label>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
            <h5 class="mt-3"><b>Debit</b></h5>
            <div id="formDebetBody" data-form="1">
                <div class="row" id="formDebet1">
                    <div class="col-md-6 form-group">
                        <label for="kodeAkun">Kode Akun</label>
                        <br>
                        <select name="code_id[]" id="kodeAkunDebet1" class="form-control select2Akun" required>
                        @foreach ($debetCodes as $code)
                        <option value="{{ $code->id }}">{{ $code->CODE }} {{ $code->NAMA_TRANSAKSI }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="nominalDebet1">Besar Nominal</label>
                        <input type="text" name="nominal[]" id="nominalDebet1" onkeypress="return isNumberKey(event)" data-type="Debet" data-form="1" class="form-control nominal" placeholder="Besar Nominal" autocomplete="off" required >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group text-right">
                    <a class="btn btn-warning btn-sm" id="addDebetBtn"><i class="fa fa-plus"></i> Tambah</a>
                    <a class="btn btn-danger btn-sm" id="delDebetBtn"><i class="fa fa-trash"></i> Hapus</a>
                </div>
            </div>
            <h5 class="mt-3"><b>Kredit</b></h5>
            <div id="formCreditBody" data-form="1">
                <div class="row" id="formCredit1">
                    <div class="col-md-6 form-group">
                        <label for="kodeAkunCredit1">Kode Akun</label>
                        <br>
                        <select name="code_id[]" id="kodeAkunCredit1" class="form-control select2Akun" required>
                        @foreach ($creditCodes as $code)
                            <option value="{{ $code->id }}">{{ $code->CODE }} {{ $code->NAMA_TRANSAKSI }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="nominalCredit1">Besar Nominal</label>
                        <input type="text" name="nominal[]" id="nominalCredit1" onkeypress="return isNumberKey(event)" data-type="Credit" data-form="1" class="form-control nominal" placeholder="Besar Nominal" autocomplete="off" required >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group text-right">
                    <a class="btn btn-warning btn-sm" id="addCreditBtn"><i class="fa fa-plus"></i> Tambah</a>
                    <a class="btn btn-danger btn-sm" id="delCreditBtn"><i class="fa fa-trash"></i> Hapus</a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mt-md-3 form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
            </div>
            <div class="col-md-12 form-group">
                <button class="btn btn-sm btn-success" id="btnSubmit"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<script src="{{ asset('js/collect.min.js') }}"></script>
<script src="{{ asset('js/cleave.min.js') }}"></script>
<script src="{{ asset('js/moment.js') }}"></script>
<script>

    $(document).ready(function ()
    {
        initiateSelect2();

        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    });

    function toRupiah(number)
    {
        var stringNumber = number.toString();
        var length = stringNumber.length;
        var temp = length;
        var res = "Rp ";
        for (let i = 0; i < length; i++) {
            res = res + stringNumber.charAt(i);
            temp--;
            if (temp % 3 == 0 && temp > 0)
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
    function initiateSelect2()
    {
        $(".select2Akun").select2({
            width: '100%',
        });
    }

    function addFormItemDebet(sectionId) 
    {
        var dataForm = $(sectionId).data('form');
        var formCounter = Number(dataForm)+1;

        var element =   '<div class="row" id="formDebet'+formCounter+'">'+
                            '<div class="col-md-6 form-group">'+
                                '<label for="kodeAkunDebet'+formCounter+'">Kode Akun</label>'+
                                '<select name="code_id[]" id="kodeAkunDebet'+formCounter+'" class="form-control select2Akun" required>'+
                                '@foreach ($debetCodes as $code)'+
                                    '<option value="{{ $code->id }}">{{ $code->CODE }} {{ $code->NAMA_TRANSAKSI }}</option>'+
                                '@endforeach'+
                                '</select>'+
                            '</div>'+
                            '<div class="col-md-6 form-group">'+
                                '<label for="nominalDebet'+formCounter+'">Besar Nominal</label>'+
                                '<input type="text" name="nominal[]" id="nominalDebet'+formCounter+'" onkeypress="return isNumberKey(event)" data-type="Debet" data-form="'+formCounter+'" class="nominal form-control" placeholder="Besar Nominal" autocomplete="off" required >'+
                                '<div class="text-danger" id="warningText"></div>'+
                            '</div>'+
                        '</div>';

        $(sectionId).data('form', formCounter);

        // add new modal
        $(sectionId).append(element);
    }

    function delFormItemDebet(sectionId) {
        var dataForm = $(sectionId).data('form');
        if (dataForm > 1) {
            $('#formDebet'+dataForm).remove();
            var formCounter = Number(dataForm)-1;
            $(sectionId).data('form', formCounter)
        }
    }

    function addFormItemCredit(sectionId) 
    {
        var dataForm = $(sectionId).data('form');
        var formCounter = Number(dataForm)+1;

        var element =   '<div class="row" id="formCredit'+formCounter+'">'+
                            '<div class="col-md-6 form-group">'+
                                '<label for="kodeAkunCredit'+formCounter+'">Kode Akun</label>'+
                                '<select name="code_id[]" id="kodeAkunCredit'+formCounter+'" class="form-control select2Akun" required>'+
                                '@foreach ($creditCodes as $code)'+
                                    '<option value="{{ $code->id }}">{{ $code->CODE }} {{ $code->NAMA_TRANSAKSI }}</option>'+
                                '@endforeach'+
                                '</select>'+
                            '</div>'+
                            '<div class="col-md-6 form-group">'+
                                '<label for="nominalCredit'+formCounter+'">Besar Nominal</label>'+
                                '<input type="text" name="nominal[]" id="nominalCredit'+formCounter+'" onkeypress="return isNumberKey(event)" data-type="Credit" data-form="'+formCounter+'" class="nominal form-control" placeholder="Besar Nominal" autocomplete="off" required >'+
                                '<div class="text-danger" id="warningText"></div>'+
                            '</div>'+
                        '</div>';

        $(sectionId).data('form', formCounter);

        // add new modal
        $(sectionId).append(element);
    }

    function delFormItemCredit(sectionId) {
        var dataForm = $(sectionId).data('form');
        if (dataForm > 1) {
            $('#formCredit'+dataForm).remove();
            var formCounter = Number(dataForm)-1;
            $(sectionId).data('form', formCounter)
        }
    }

    $(document).on('keyup', '.nominal', function () 
    {
        var nominal = $(this).val().toString();
        var dataForm = $(this).data('form');
        var dataType = $(this).data('type');
        nominal = nominal.replace(/[^\d]/g, "",'');
        $('#nominal'+ dataType + dataForm).val(toRupiah(nominal));
    });

    $('#addDebetBtn').on('click', function () {
        addFormItemDebet('#formDebetBody');
        initiateSelect2();
    });

    $('#delDebetBtn').on('click', function () {
        delFormItemDebet('#formDebetBody');
    });

    $('#addCreditBtn').on('click', function () {
        addFormItemCredit('#formCreditBody');
        initiateSelect2();
    });

    $('#delCreditBtn').on('click', function () {
        delFormItemCredit('#formCreditBody');
    });

</script>
@stop