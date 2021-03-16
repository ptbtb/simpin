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
            <li class="breadcrumb-item"><a href="{{ route('saldo-awal-list') }}">Saldo Awal</a></li>
            <li class="breadcrumb-item active">Tambah Saldo Awal</li>
        </ol>
    </div>
</div>
@endsection

@section('css')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('saldo-awal-create') }}" method="POST">
            @csrf
            <div id="formBody" data-form="1">
                <div class="row" id="form1">
                    <div class="col-md-6 form-group">
                        <label for="kodeAkun">Kode Akun</label>
                        <select name="code_id[]" id="kodeAkun1" class="form-control select2Akun" required>
                        @foreach ($codes as $code)
                            <option value="{{ $code->id }}">{{ $code->CODE }} {{ $code->NAMA_TRANSAKSI }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="nominal1">Besar Nominal</label>
                        <input type="text" name="nominal[]" id="nominal1" data-form="1" class="form-control nominal" placeholder="Besar Nominal" autocomplete="off" required >
                        <div class="text-danger" id="warningText"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 form-group text-right">
                    <a class="btn btn-warning btn-sm" id="addBtn"><i class="fa fa-plus"></i> Tambah</a>
                    <a class="btn btn-danger btn-sm" id="delBtn"><i class="fa fa-trash"></i> Hapus</a>
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
    });

    function toRupiah(field)
    {
        new Cleave(field, {
            numeralDecimalMark: ',',
            delimiter: '.',
            numeral: true,
            numeralThousandsGroupStyle: 'thousand',
        });
    }

    // formating thousand
    $('.nominal').toArray().forEach(function(field){
        toRupiah(field);
    });

    function initiateSelect2()
    {
        $(".select2Akun").select2({
            width: '100%',
        });
    }

    function addFormItem(sectionId) 
    {
        var dataForm = $(sectionId).data('form');
        var formCounter = Number(dataForm)+1;

        var element =   '<div class="row" id="form'+formCounter+'">'+
                            '<div class="col-md-6 form-group">'+
                                '<label for="kodeAkun">Kode Akun</label>'+
                                '<select name="code_id[]" id="kodeAkun" class="form-control select2Akun" required>'+
                                '@foreach ($codes as $code)'+
                                    '<option value="{{ $code->id }}">{{ $code->CODE }} {{ $code->NAMA_TRANSAKSI }}</option>'+
                                '@endforeach'+
                                '</select>'+
                            '</div>'+
                            '<div class="col-md-6 form-group">'+
                                '<label for="nominal'+formCounter+'">Besar Nominal</label>'+
                                '<input type="text" name="nominal[]" id="nominal'+formCounter+'" data-form="'+formCounter+'" class="form-control nominal" placeholder="Besar Nominal" autocomplete="off" required >'+
                                '<div class="text-danger" id="warningText"></div>'+
                            '</div>'+
                        '</div>';

        $(sectionId).data('form', formCounter);

        // add new modal
        $(sectionId).append(element);

        // add thousand separator
        toRupiah($('#form'+formCounter+' .nominal'));
    }

    function delFormItem(sectionId) {
        var dataForm = $(sectionId).data('form');
        if (dataForm > 1) {
            $('#form'+dataForm).remove();
            var formCounter = Number(dataForm)-1;
            $(sectionId).data('form', formCounter)
        }
    }

    $('#addBtn').on('click', function () {
        addFormItem('#formBody');
        initiateSelect2();
    });

    $('#delBtn').on('click', function () {
        delFormItem('#formBody');
    });

</script>
@stop