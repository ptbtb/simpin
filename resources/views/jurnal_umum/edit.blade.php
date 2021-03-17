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
            <li class="breadcrumb-item active">Edit Jurnal Umum</li>
        </ol>
    </div>
</div>
@endsection

@section('css')
@endsection
@section('plugins.Sweetalert2', true)

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('jurnal-umum-edit', ['id' => $jurnalUmum->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row" id="form1">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="tgl_transaksi">Tgl Transaksi</label>
                        <input id="tgl_transaksi" type="date" name="tgl_transaksi" class="form-control" placeholder="yyyy-mm-dd" value="{{ $jurnalUmum->tgl_transaksi->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="nominal1">Deskripsi</label>
                        <input type="text" maxlength="255" name="deskripsi" id="deskripsi" class="form-control" placeholder="Deskripsi" autocomplete="off" value="{{ $jurnalUmum->deskripsi }}" required>
                    </div>
                    <div id="formLampiranBody" data-form="{{ count($jurnalUmum->jurnalUmumLampirans) }}">
                        @foreach ($jurnalUmum->jurnalUmumLampirans as $key => $jurnalUmumLampiran)
                        <div class="row" id="formLampiran{{ $key + 1 }}">
                            <div class="form-group col-md-11">
                                <label>Lampiran {{ $key + 1 }}</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="lampiran{{ $key + 1 }}" name="lampiran[]" accept="application/pdf" style="cursor: pointer" >
                                    <input type="hidden" name="lampiranCounts[]" value="1">
                                    <label class="custom-file-label" for="customFile">Choose Document</label>
                                </div>
                            </div>
                            <div class="form-group col-md-1 mt-2">
                                <br>
                                <a class="btn btn-warning btn-sm" href="{{ asset($jurnalUmumLampiran->lampiran) }}" target="_blank"><i class="fa fa-file"></i></a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="form-group text-right">
                        <a class="btn btn-warning btn-sm" id="addLampiranBtn"><i class="fa fa-plus"></i> Tambah</a>
                        <a class="btn btn-danger btn-sm" id="delLampiranBtn"><i class="fa fa-trash"></i> Hapus</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <label for=""></label>
                    <div class="form-group">
                        
                    </div>
                </div>
            </div>
            <hr>
            <h5 class="mt-3"><b>Debit</b></h5>
            <div id="formDebetBody" data-form="{{ $itemDebets->count() }}">
                @foreach ($itemDebets as $key => $itemDebet)
                    <div class="row" id="formDebet{{ $loop->iteration }}">
                        <div class="col-md-6 form-group">
                            <label for="kodeAkun">Kode Akun</label>
                            <br>
                            <select name="code_id[]" id="kodeAkunDebet{{ $loop->iteration }}" class="form-control select2Akun" required>
                            @foreach ($debetCodes as $code)
                                <option value="{{ $code->id }}" @if($code->id == $itemDebet->code_id) selected @endif>{{ $code->CODE }} {{ $code->NAMA_TRANSAKSI }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="nominalDebet{{ $loop->iteration }}">Besar Nominal</label>
                            <input type="text" name="nominal[]" id="nominalDebet{{ $loop->iteration }}" value="{{ $itemDebet->nominal }}" data-type="Debet" data-form="{{ $loop->iteration }}" class="form-control nominal" placeholder="Besar Nominal" autocomplete="off" required >
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="row">
                <div class="col-md-12 form-group text-right">
                    <a class="btn btn-warning btn-sm" id="addDebetBtn"><i class="fa fa-plus"></i> Tambah</a>
                    <a class="btn btn-danger btn-sm" id="delDebetBtn"><i class="fa fa-trash"></i> Hapus</a>
                </div>
            </div>
            <h5 class="mt-3"><b>Kredit</b></h5>
            <div id="formCreditBody" data-form="{{ $itemCredits->count() }}">
                @foreach ($itemCredits as $key => $itemCredit)
                <div class="row" id="formCredit{{ $loop->iteration }}">
                    <div class="col-md-6 form-group">
                        <label for="kodeAkunCredit{{ $loop->iteration }}">Kode Akun</label>
                        <br>
                        <select name="code_id[]" id="kodeAkunCredit{{ $loop->iteration }}" class="form-control select2Akun" required>
                        @foreach ($creditCodes as $code)
                            <option value="{{ $code->id }}" @if($code->id == $itemCredit->code_id) selected @endif>{{ $code->CODE }} {{ $code->NAMA_TRANSAKSI }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="nominalCredit{{ $loop->iteration }}">Besar Nominal</label>
                        <input type="text" name="nominal[]" id="nominalCredit{{ $loop->iteration }}" value="{{ $itemCredit->nominal }}" data-type="Credit" data-form="{{ $loop->iteration }}" class="form-control nominal" placeholder="Besar Nominal" autocomplete="off" required >
                    </div>
                </div>
                @endforeach
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
                <button class="btn btn-sm btn-success" id="btnSubmit"><i class="fas fa-save"></i> Update</button>
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

    // form validation
    $( "form" ).submit(function( event )  
    {
        checkBalance();
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
                                '<input type="text" name="nominal[]" id="nominalDebet'+formCounter+'" data-type="Debet" data-form="'+formCounter+'" class="nominal form-control" placeholder="Besar Nominal" autocomplete="off" required >'+
                                '<div class="text-danger" id="warningText"></div>'+
                            '</div>'+
                        '</div>';

        $(sectionId).data('form', formCounter);

        // add new modal
        $(sectionId).append(element);

        // add thousand separator
        toRupiah($('#formDebet'+formCounter+' .nominal'));
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
                                '<input type="text" name="nominal[]" id="nominalCredit'+formCounter+'" data-type="Credit" data-form="'+formCounter+'" class="nominal form-control" placeholder="Besar Nominal" autocomplete="off" required >'+
                                '<div class="text-danger" id="warningText"></div>'+
                            '</div>'+
                        '</div>';

        $(sectionId).data('form', formCounter);

        // add new modal
        $(sectionId).append(element);

        // add thousand separator
        toRupiah($('#formCredit'+formCounter+' .nominal'));
    }

    function delFormItemCredit(sectionId) {
        var dataForm = $(sectionId).data('form');
        if (dataForm > 1) {
            $('#formCredit'+dataForm).remove();
            var formCounter = Number(dataForm)-1;
            $(sectionId).data('form', formCounter)
        }
    }

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

    function checkBalance() 
    {
        // count form item
        var totalDebetForm = $('#formDebetBody').data('form');
        var totalCreditForm = $('#formCreditBody').data('form');
        var totalDebet = 0;
        var totalCredit = 0;
        
        // looping every debet item
        for (let index = 1; index <= totalDebetForm; index++) 
        {
            var nominal = parseInt($('#nominalDebet' + index).val().replace(/[^\d]/g, "",''));
            totalDebet += nominal;
        }

        // looping every credit item
        for (let index = 1; index <= totalCreditForm; index++) 
        {
            var nominal = parseInt($('#nominalCredit' + index).val().replace(/[^\d]/g, "",''));
            totalCredit += nominal;
        }

        // debet and credit must balance
        if(parseInt(totalDebet) != parseInt(totalCredit))
        {
            event.preventDefault();

            Swal.fire({
                type: 'error',
                title: 'Error!',
                html: 'Total Debet dan Kredit harus balance. <br> Debet: Rp ' + new Intl.NumberFormat(['ban', 'id']).format(totalDebet) + '<br> Kredit: Rp '+ new Intl.NumberFormat(['ban', 'id']).format(totalCredit) ,
                showConfirmButton: true
            }).then((result) => {

            });
        }
    }
    
    function addFormLampiran(sectionId) 
    {
        var dataForm = $(sectionId).data('form');
        var formCounter = Number(dataForm)+1;

        var element =   '<div class="form-group" id="formLampiran'+formCounter+'">'+
                            '<label>Lampiran '+formCounter+'</label>'+
                            '<div class="custom-file">'+
                                '<input type="file" class="custom-file-input" id="lampiran'+formCounter+'" name="lampiran[]" accept="application/pdf" style="cursor: pointer" required>'+
                                '<label class="custom-file-label" for="customFile">Choose Document</label>'+
                                '<input type="hidden" name="lampiranCounts[]" value="1">'+
                            '</div>'+
                        '</div>';

        $(sectionId).data('form', formCounter);

        // add new modal
        $(sectionId).append(element);
    }

    function delFormLampiran(sectionId) {
        var dataForm = $(sectionId).data('form');
        if (dataForm > 1) {
            $('#formLampiran'+dataForm).remove();
            var formCounter = Number(dataForm)-1;
            $(sectionId).data('form', formCounter)
        }
    }

    $('#addLampiranBtn').on('click', function () {
        addFormLampiran('#formLampiranBody');

        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    });

    $('#delLampiranBtn').on('click', function () {
        delFormLampiran('#formLampiranBody');
    });

</script>
@stop