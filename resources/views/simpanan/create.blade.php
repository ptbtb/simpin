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
                    <div class="col-md-6 form-group">
                        <label for="kodeAnggota">Anggota</label>
                        <select name="kode_anggota" id="kodeAnggota" class="form-control" required>
                            <option value="">Pilih salah satu</option>
                        </select>
                        <div class="text-danger" id="warningTextAnggota"></div>
                    </div>
                @endif
                <div class="col-md-6 form-group">
                    <label for="jenisSimpanan">Jenis Simpanan</label>
                    <select name="jenis_simpanan" id="jenisSimpanan" class="form-control" disabled=true required>
                        <option value="">Pilih salah satu</option>
                        @foreach ($listJenisSimpanan as $jenisSimpanan)
                            <option value="{{ $jenisSimpanan->kode_jenis_simpan }}">{{ strtoupper($jenisSimpanan->nama_simpanan) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 form-group">
                    <label for="besarSimpanan">Besar Simpanan</label>
                    <input type="text" name="besar_simpanan" id="besarSimpanan" onkeypress="return isNumberKey(event)"  class="form-control" placeholder="Besar Simpanan" autocomplete="off" required disabled >
                    <div class="text-danger" id="warningText"></div>
                </div>
                <div class="col-md-6" id="angsuranSimpanan">
                    <label for="besarSimpanan">Detail Informasi</label>
                    <div class="row col-md-6 mb-3" id="detailAngsuran">
                    </div>
                </div>
                <div class="col-md-12 form-group">
                    <label for="keterangan">Keterangan</label>
                    <textarea name="keterangan" id="keterangan" rows="5" class="form-control"></textarea>
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
<script src="{{ asset('js/collect.min.js') }}"></script>+
<script src="{{ asset('js/cleave.min.js') }}"></script>
<script>
    var jenisSimpanan = collect({!!$listJenisSimpanan!!});
    var anggotaId;
    var besarSimpananSukarela;
    var besarSimpananPokok;
    var tipeSimpanan;
    $(document).ready(function ()
    {
        initiateSelect2();
         $('#angsuranSimpanan').hide();
         $('#warningText').hide();

    });

    $('#jenisSimpanan').on('change', function ()
    {
        var selectedValue = $(this).children("option:selected").val();
        $('#besarSimpanan').val('');
        $('#warningText').hide();
        callSavingPaymentValue(anggotaId, selectedValue);
    });

    $('#kodeAnggota').on('change', function (){
        if ($(this).children("option:selected").val() != anggotaId){
            $('#jenisSimpanan').val('');
            $('#besarSimpanan').val('');
            $('#warningText').hide();

            anggotaId = $(this).children("option:selected").val();
            callDetailAnggota(anggotaId);
            $("#jenisSimpanan").prop('disabled', false);
        }
    })

    $('#besarSimpanan').on('keyup', function ()
    {
        var besarSimpanan = $(this).val().toString();
        besarSimpanan = besarSimpanan.replace(/[^\d]/g, "",'');
        $('#besarSimpanan').val(toRupiah(besarSimpanan));
        if((tipeSimpanan === '502.01.000') || (tipeSimpanan === '409.01.000') || ( tipeSimpanan === '409.03.000')){
            if(besarSimpanan > besarSimpananSukarela) {
                $('#warningText').text('Besar Simpanan Maksimal 65% dari Total Gaji/bulan');
                $('#warningText').show();
                $('#btnSubmit').prop('disabled', true);
            } else {
                $('#warningText').hide();
                $('#btnSubmit').prop('disabled', false);
            }
        }
        if(tipeSimpanan === '411.01.000'){
            if(besarSimpanan > besarSimpananPokok) {
                $('#warningText').text('Besar Simpanan Maksimal Sisa Angsuran');
                $('#warningText').show();
                $('#btnSubmit').prop('disabled', true);
                
            } else {
                $('#warningText').hide();
                $('#btnSubmit').prop('disabled', false);
            }
        }
    });

    function initiateSelect2()
    {
        $("#jenisSimpanan").select2({
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
                if (!response.hasOwnProperty('gaji_bulanan')){
                    $('#jenisSimpanan').prop('disabled', true);
                    $('#btnSubmit').prop('disabled', true);
                    $('#warningTextAnggota').text('Anggota ini belum memiliki gaji bulanan');
                    $('#warningTextAnggota').show();
                }
                else {
                    $('#jenisSimpanan').prop('disabled', false);
                    $('#btnSubmit').prop('disabled', false  );
                    $('#warningTextAnggota').hide();
                }
            }  
        })
    }

    function callSavingPaymentValue(anggotaId, type){
        tipeSimpanan = type;

        return $.ajax({
            url: '{{ route('ajax-simpanan-payment-value') }}',
            dataType: 'json',
            data: {
                'anggotaId' : anggotaId,
                'type' : type
            },
            success: function (response) {
                if(response) {
                    $("#besarSimpanan").prop('disabled', false);
                    // simpanan wajib
                    if(type === '411.12.000') {
                        const paymentValue = response.paymentValue;
                        $('#besarSimpanan').val(toRupiah(paymentValue));
                        $('#besarSimpanan').attr('readonly',true);
                        $('#angsuranSimpanan').hide();
                    }
                    // simpanan pokok
                    if(type === '411.01.000') {
                        const angsuranSimpanan = response.attribute;
                        const paymentValue = response.paymentValue;
                        besarSimpananPokok = paymentValue;
                        
                        $('#besarSimpanan').val(toRupiah(paymentValue));
                        $('#detailAngsuran').empty();
                        angsuranSimpanan.map(val => {
                            $('#detailAngsuran').append('<div class="col-md-6">Angsuran Ke - ' + val.angsuran_ke + '</div>');
                            $('#detailAngsuran').append('<div class="col-md-6"> ' + toRupiah(val.besar_angsuran) + '</div>');
                        })
                        $('#detailAngsuran').append('<div class="col-md-6"> Sisa Angsuran </div>');
                        $('#detailAngsuran').append('<div class="col-md-6">' + toRupiah(paymentValue) + '</div>');
                        $('#angsuranSimpanan').show();
                        if(angsuranSimpanan.length === 3) {
                            $('#besarSimpanan').attr('readonly',true);
                        } else {
                            $('#besarSimpanan').attr('readonly',false);
                        }
                      
    
                    }
                    // simpanan sukarela / khusus
                    if(type ===  '502.01.000' || type === '409.01.000' || type === '409.03.000'  ) {
                        const paymentValue = response.paymentValue;
                        besarSimpananSukarela = response.paymentValue; 
                        $('#besarSimpanan').val(toRupiah(paymentValue));
                        $('#angsuranSimpanan').hide();
                        $('#besarSimpanan').attr('readonly',false);

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
</script>
@stop