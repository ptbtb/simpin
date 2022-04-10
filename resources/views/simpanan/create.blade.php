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
            <li class="breadcrumb-item"><a href="">Simpanan</a></li>
			<li class="breadcrumb-item active">Tambah Transaksi</li>
		</ol>
	</div>
</div>
@endsection

@section('css')
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="" method="POST">
            @csrf
            <div class="row">
                @if ($request->kode_anggota)
                    <div class="col-md-12 form-group">
                        <label for="kodeAnggota">Kode Anggota</label>
                        <select name="kode_anggota" id="kodeAnggota" class="form-control" required>
                            <option value="{{ $anggota->kode_anggota }}">{{ $anggota->nama_anggota }}</option>
                        </select>
                        <div class="text-danger" id="warningTextAnggota"></div>
                    </div>
                @else
                    <div class="col-md-12 form-group">
                        <label for="kodeAnggota">Anggota</label>
                        <select name="kode_anggota" id="kodeAnggota" class="form-control" required>
                            <option value="">Pilih salah satu</option>
                        </select>
                        <div class="text-danger" id="warningTextAnggota"></div>
                    </div>
                @endif
                <div class="col-12" id="multipleForm">
                    <div class="row childForm" id="form1" data-id="1">
                        <div class="col-md-6 form-group">
                            <label>Jenis Simpanan</label>
                            <select name="jenis_simpanan[]" data-form="1" class="form-control jenisSimpanan" disabled=true required>
                                <option value="">Pilih Satu</option>
                                @foreach ($listJenisSimpanan as $jenisSimpanan)
                                    <option value="{{ $jenisSimpanan->kode_jenis_simpan }}">{{ strtoupper($jenisSimpanan->nama_simpanan) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group" id="besaSimpananDetail1">
                            <label>Besar Simpanan</label>
                            <input type="text" name="besar_simpanan[]" data-form="1" onkeypress="return isNumberKey(event)"  class="form-control besarSimpanan" placeholder="Besar Simpanan" autocomplete="off" required disabled>
                            <div class="text-danger" id="warningText1"></div>
                        </div>
                        <div class="col-md-12 form-group" id="periodeDetail1">
                            <label for="periode">Periode</label>
                            <input type="text" name="periode[]" class="form-control periode" placeholder="Periode" autocomplete="off">
                        </div>
                        <div class="col-md-12" id="angsuranSimpanan1">
                            <label>Detail Informasi</label>
                            <div class="row col-md-6 mb-3" id="detailAngsuran1">
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Jenis Akun</label>
                            <select name="jenis_akun[]" data-form="1" class="form-control select2 jenisAkun" required>
                                @foreach ($listSumberDana as $sumberDana)
                                    <option value="{{ $sumberDana->id }}">{{ $sumberDana->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Akun</label>
                            <select name="id_akun_debet[]" class="form-control select2 code" required>
                                <option value="" selected disabled>Pilih Akun</option>
                            </select>
                        </div>
                        <div class="col-12 form-group">
                            <label>Tgl Transaksi</label>
                            <input type="text" name="tgl_transaksi[]" class="form-control datepicker" autocomplete="off" required>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="keterangan">Keterangan</label>
                            <textarea name="keterangan[]" rows="5" class="form-control keterangan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-12 text-right">
                    <a class="btn btn-sm btn-danger btn-delete text-white"><i class="fa fa-trash"></i> Delete</a>
                    <a class="btn btn-sm btn-info btn-add text-white"><i class="fa fa-plus"></i> Add</a>
                </div>
                <div class="col-md-12 mt-md-3 form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <div class="col-md-12 form-group">
                    <button class="btn btn-sm btn-success" id="btnSubmit"><i class="fas fa-save"></i> Simpan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@stop

@section('js')
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="{{ asset('js/collect.min.js') }}"></script>
<script src="{{ asset('js/cleave.min.js') }}"></script>
<script src="{{ asset('js/moment.js') }}"></script>
<script>
    var jenisSimpanan = collect({!!$listJenisSimpanan!!});
    var anggotaId;
    var besarSimpananSukarela;
    var besarSimpananPokok;
    var tipeSimpanan;
    var today;
    var listSumberDana = collect(@json($listSumberDana));

    $(document).ready(function ()
    {
        initiateSelect2();
         $('#angsuranSimpanan1').hide();
         $('#warningText1').hide();
         $('#periodeDetail1').hide();

        $('.jenisAkun').trigger( "change" );
        today = new Date(new Date().getFullYear(), new Date().getMonth(), new Date().getDate());
    });

    $('#kodeAnggota').on('change', function (){
        if ($(this).children("option:selected").val() != anggotaId)
        {
            $('.jenisSimpanan').val('');
            $('.besarSimpanan').val('');
            $('#warningText1').hide();

            anggotaId = $(this).children("option:selected").val();
            callDetailAnggota(anggotaId);
            $(".jenisSimpanan").prop('disabled', false);
        }
    })

    $(document).on('change', '.jenisSimpanan', function ()
    {
        var data_form = $(this).data('form');
        var selectedValue = $(this).children("option:selected").val();
        $('#form'+data_form+' .besarSimpanan').val('');
        $('#warningText'+data_form).hide();
        callSavingPaymentValue(anggotaId, selectedValue, data_form);
    });

    $(document).on('keyup', '.besarSimpanan', function ()
    {
        var besarSimpanan = $(this).val().toString();
        var data_form = $(this).data('form');
        besarSimpanan = besarSimpanan.replace(/[^\d]/g, "",'');
        $(this).val(toRupiah(besarSimpanan));
        /*if(tipeSimpanan === '502.01.000'){
            if(besarSimpanan > besarSimpananSukarela) {
                errMessage('warningText', 'Jumlah besar simpanan melebihi 65% dari total gaji/bulan');
            } else {
                clearErrMessage('warningText');
            }
        }*/
        if(tipeSimpanan === '411.01.000')
        {
            if(parseInt(besarSimpanan) > parseInt(besarSimpananPokok))
            {
                errMessage('warningText'+data_form, 'Jumlah besar simpanan melebihi sisa angsuran');

            }
            else
            {
                clearErrMessage('warningText'+data_form,);
            }
        }
    });

    // trigger to get kas or bank select option
    $(document).on('change', '.jenisAkun', function ()
    {
        var data_form = $(this).data('form');

        // remove all option in code
        $('#form'+data_form+' .code').empty();

        // get jenis akun
        var jenisAkun = $(this).val();
        selectedSumberDana = listSumberDana.where('id', parseInt(jenisAkun)).first();
        currentCodes = collect(selectedSumberDana.codes);

        var pattern = "";
        currentCodes.each(function (code)
        {
            if(code.id == 22)
            {
                pattern = pattern + '<option value="'+ code.id +'" selected>'+ code.CODE +' '+ code.NAMA_TRANSAKSI +'</option>';
            }
            else
            {
                pattern = pattern + '<option value="'+ code.id +'">'+ code.CODE +' '+ code.NAMA_TRANSAKSI +'</option>';
            }
        });
        $('#form'+data_form+' .code').html(pattern);
        /* 
        if(jenisAkun == 2)
        {
            // loop through code bank
            $.each(bankAccountArray, function(key, bankAccount)
            {
                // set dafault to 102.18.000
                if(bankAccount.id == 22)
                {
                    var selected = 'selected';
                }
                else
                {
                    var selected = '';
                }

                // insert new option
                $('#form'+data_form+' .code').append('<option value="'+bankAccount.id+'"'+ selected +'>'+bankAccount.code+ ' ' + bankAccount.name + '</option>');
            });
        }
        else if(jenisAkun == 1)
        {
            // insert new option
            $('#form'+data_form+' .code').append('<option value="4" >101.01.102 KAS SIMPAN PINJAM</option><option value="153" >404.08.000 SETORAN BELUM RINCI</option>');
        }else if(jenisAkun == 3)
        {
            // insert new option
            $('#form'+data_form+' .code').append('<option value="174" >409.01.000 SIMPANAN KHUSUS</option><option value="182" >409.03.000 SIMPANAN KHUSUS PAGU</option><option value="133" >402.01.000 R/K KOPEGMAR</option>');
        }
         */
        $('#form'+data_form+' .code').trigger( "change" );
    });

    // trigger button add form
    $(document).on('click', '.btn-add', function ()
    {
        // get latest child of multiple form
        var data_form = $('#multipleForm .childForm:last').data('id');
        data_form = parseInt(data_form) + 1;
        addElement(data_form);
    });

    // trigger button delete form
    $(document).on('click', '.btn-delete', function ()
    {
        // get latest child of multiple form
        var data_form = $('#multipleForm .childForm:last').data('id');
        data_form = parseInt(data_form);
        if (data_form > 1)
        {
            $('#form'+data_form).remove();
        }
    });

    function initiateSelect2()
    {
        $(".jenisSimpanan").select2({
            placeholder: 'pilih salah satu',
            ajax: {
                url: '{{ route('jenis-simpanan-searchByUser') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        userId: anggotaId
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

        @if(!$request->kode_anggota)
            $("#kodeAnggota").select2({
                ajax: {
                    placeholder: "Select a state",
                    url: '{{ route('anggota-ajax-search') }}',
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
        @else
            $("#kodeAnggota").select2();
        anggotaId={{$request->kode_anggota}};
         callDetailAnggota(anggotaId);
        @endif
    }

    function callDetailAnggota(anggotaId){
        return $.ajax({
            url: '{{ route('anggota-ajax-getDetail') }}',
            dataType: 'json',
            data: {
                'anggotaId' : anggotaId
            },
            success: function (response) {
                if (response.kelas == null)
                {
                    if (response.id_jenis_anggota=={{ JENIS_ANGGOTA_PENSIUNAN }})
                    {
                         $('.jenisSimpanan').prop('disabled', false);
                        errMessage('warningTextAnggota', '');
                        $('#btnSubmit').prop('disabled', false);
                    }
                    else
                    {
                        $('.jenisSimpanan').prop('disabled', true);
                        errMessage('warningTextAnggota', 'Mohon Diisi dahulu Unit dan Kelas Unit ');
                    }

                }
                else
                {
                    $('.jenisSimpanan').prop('disabled', false);
                    clearErrMessage('warningTextAnggota');
                }
            }
        })
    }

    function callSavingPaymentValue(anggotaId, type, data_form){
        tipeSimpanan = type;

        $.ajax({
            url: '{{ route('ajax-simpanan-payment-value') }}',
            dataType: 'json',
            data:
            {
                'anggotaId' : anggotaId,
                'type' : type
            },
            success: function (response)
            {
                if(response)
                {
                    $('#form'+data_form+' .besarSimpanan').prop('disabled', false);
                    // simpanan wajib
                    if(type === '{{ JENIS_SIMPANAN_WAJIB }}')
                    {
                        const paymentValue = response.paymentValue;
                        // jika belum pernah ada transaksi simpanan wajib
                        if (response.attribute == null)
                        {
                            var latestPayment = moment();
                            var monthYear = moment(latestPayment).format('MMMM YYYY');
                            var dateMonthYear = moment(latestPayment).format('YYYY-MM-DD');
                        }
                        else
                        {
                            var latestPayment = response.attribute.periode || response.attribute.tanggal_entri;
                            var monthYear = moment(latestPayment).add(1, 'months').format('MMMM YYYY');
                            var dateMonthYear = moment(latestPayment).add(1, 'months').format('YYYY-MM-DD');
                        }
                        $('#form'+data_form+' .periode').val(dateMonthYear);
                        $('#form'+data_form+' .besarSimpanan').val(toRupiah(paymentValue));
                        $('#form'+data_form+' .besarSimpanan').attr('readonly',false);
                        $('#angsuranSimpanan'+data_form).hide();
                        $('#periodeDetail'+ data_form).show();
                    }
                    // simpanan pokok
                    if(type === '{{ JENIS_SIMPANAN_POKOK }}')
                    {
                        const angsuranSimpanan = response.attribute;
                        const paymentValue = response.paymentValue;
                        besarSimpananPokok = paymentValue;

                        $('#form'+data_form+' .besarSimpanan').val(toRupiah(paymentValue));
                        $('#detailAngsuran'+data_form).empty();
                        angsuranSimpanan.map(val => {
                            $('#detailAngsuran'+data_form).append('<div class="col-md-6">Angsuran Ke - ' + val.angsuran_ke + '</div>');
                            $('#detailAngsuran'+data_form).append('<div class="col-md-6"> ' + toRupiah(val.besar_angsuran) + '</div>');
                        })
                        $('#detailAngsuran'+data_form).append('<div class="col-md-6"> Sisa Angsuran </div>');
                        $('#detailAngsuran'+data_form).append('<div class="col-md-6">' + toRupiah(paymentValue) + '</div>');

                        $('#angsuranSimpanan'+data_form).show();
                        $('#periodeDetail'+data_form).show();

                        if(angsuranSimpanan.length === 3)
                        {
                            $('#form'+data_form+' .besarSimpanan').attr('readonly',true);
                        }
                        else
                        {
                            $('#form'+data_form+' .besarSimpanan').attr('readonly',false);
                        }
                    }
                    // simpanan sukarela
                    if(type ===  '{{ JENIS_SIMPANAN_SUKARELA }}')
                    {
                        const paymentValue = response.paymentValue;
                        besarSimpananSukarela = response.paymentValue;
                        if (response.attribute == null)
                        {
                            var latestPayment = moment();
                            var monthYear = moment(latestPayment).format('MMMM YYYY');
                            var dateMonthYear = moment(latestPayment).format('YYYY-MM-DD');
                        }
                        else
                        {
                            var latestPayment = response.attribute.periode || response.attribute.tanggal_entri;
                            var monthYear = moment(latestPayment).add(1, 'months').format('MMMM YYYY');
                            var dateMonthYear = moment(latestPayment).add(1, 'months').format('YYYY-MM-DD');
                        }
                        $('#form'+data_form+' .besarSimpanan').val(toRupiah(paymentValue));
                        $('#form'+data_form+' .periode').val(dateMonthYear);
                        $('#angsuranSimpanan'+data_form).hide();
                        $('#periodeDetail'+data_form).show();
                        $('#form'+data_form+' .besarSimpanan').attr('readonly',false);

                    }
                    if(type ===  '409.01.000')
                    {
                        var dateMonthYear = moment(latestPayment).format('YYYY-MM-DD');
                        $('#form'+data_form+' .periode').val('');
                        $('#angsuranSimpanan'+data_form).hide();
                        $('#periodeDetail'+data_form).show();

                        $('#form'+data_form+' .besarSimpanan').attr('readonly',false);
                    }
                }
            }
        })
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

    function errMessage(idElement, message)
    {
        $('#' +idElement).text(message);
        $('#' +idElement).show();
        $('#btnSubmit').prop('disabled', true);
    }

    function clearErrMessage(idElement, message)
    {
        $('#' +idElement).hide();
        $('#btnSubmit').prop('disabled', false);
    }

    $(".select2").select2({
        width: '100%',
    });

    // code array
    var bankAccountArray = [];

    // get bank account number from php
    @foreach($bankAccounts as $key => $bankAccount)
        bankAccountArray[{{ $loop->index }}]={ id : {{ $bankAccount->id }}, code: '{{ $bankAccount->CODE }}', name: '{{ $bankAccount->NAMA_TRANSAKSI }}' };
    @endforeach

    // initiate datepicker
    $('.datepicker').datepicker({
        maxDate: today,
        uiLibrary: 'bootstrap4',
        format: 'dd-mm-yyyy'
    });

    function addElement(data_form)
    {
        var string = '<div class="row childForm" id="form'+data_form+'" data-id="'+data_form+'">' +
                        '<div class="col-12"><hr class="my-2" style="border-width: 2px; border-color: #000;"></div>'+
                        '<div class="col-md-6 form-group">' +
                            '<label>Jenis Simpanan</label>' +
                            '<select name="jenis_simpanan[]" data-form="'+data_form+'" class="form-control jenisSimpanan" required>' +
                                '<option value="">Pilih Satu</option>' +
                                @foreach ($listJenisSimpanan as $jenisSimpanan)
                                    '<option value="{{ $jenisSimpanan->kode_jenis_simpan }}">{{ strtoupper($jenisSimpanan->nama_simpanan) }}</option>' +
                                @endforeach
                            '</select>' +
                        '</div>' +
                        '<div class="col-md-6 form-group" id="besaSimpananDetail'+data_form+'">' +
                            '<label>Besar Simpanan</label>' +
                            '<input type="text" name="besar_simpanan[]" data-form="'+data_form+'" onkeypress="return isNumberKey(event)"  class="form-control besarSimpanan" placeholder="Besar Simpanan" autocomplete="off" required disabled>' +
                            '<div class="text-danger" id="warningText'+data_form+'"></div>' +
                        '</div>' +
                        '<div class="col-md-12 form-group" id="periodeDetail'+data_form+'">' +
                            '<label for="periode">Periode</label>' +
                            '<input type="text" name="periode[]" class="form-control periode" placeholder="Periode" autocomplete="off">' +
                        '</div>' +
                        '<div class="col-md-12" id="angsuranSimpanan'+data_form+'">' +
                            '<label>Detail Informasi</label>' +
                            '<div class="row col-md-6 mb-3" id="detailAngsuran'+data_form+'">' +
                            '</div>' +
                        '</div>' +
                        '<div class="col-md-6 form-group">' +
                            '<label>Jenis Akun</label>' +
                            '<select name="jenis_akun[]" data-form="'+data_form+'" class="form-control select2 jenisAkun" required>' +
                                '@foreach ($listSumberDana as $sumberDana)' +
                                    '<option value="{{ $sumberDana->id }}">{{ $sumberDana->name }}</option>' +
                                '@endforeach' +
                            '</select>' +
                        '</div>' +
                        '<div class="col-md-6 form-group">' +
                            '<label>Akun</label>' +
                            '<select name="id_akun_debet[]" class="form-control select2 code" required>' +
                                '<option value="" selected disabled>Pilih Akun</option>' +
                            '</select>' +
                        '</div>' +
                        '<div class="col-12 form-group">' +
                            '<label>Tgl Transaksi</label>' +
                            '<input type="text" name="tgl_transaksi[]" class="form-control datepicker" autocomplete="off" required>' +
                        '</div>' +
                        '<div class="col-md-12 form-group">' +
                            '<label for="keterangan">Keterangan</label>' +
                            '<textarea name="keterangan[]" rows="5" class="form-control keterangan"></textarea>' +
                        '</div>' +
                    '</div>';

        $('#multipleForm').append(string);
        $("#form"+data_form+" .datepicker").datepicker({
            maxDate: today,
            uiLibrary: 'bootstrap4',
            format: 'dd-mm-yyyy'
        });
        $('.jenisAkun').trigger( "change" );
        $("#form"+data_form+" .jenisSimpanan").select2({
            placeholder: 'pilih salah satu',
            ajax: {
                url: '{{ route('jenis-simpanan-searchByUser') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public',
                        userId: anggotaId
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
</script>
@stop
