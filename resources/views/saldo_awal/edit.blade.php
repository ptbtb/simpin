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
        <form action="{{ route('saldo-awal-edit', ['id' => $saldoAwal->id]) }}" method="POST">
            @csrf
            <div id="formBody" data-form="1">
                <div class="row" id="form1">
                    <div class="col-md-6 form-group">
                        <label for="kodeAkun">Kode Akun</label>
                        <select name="code_id" id="kodeAkun1" class="form-control select2Akun" required>
                        @foreach ($codes as $code)
                            <option value="{{ $code->id }}" @if($code->id == $saldoAwal->code_id) selected @endif>{{ $code->CODE }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="nominal1">Besar Nominal</label>
                    <input type="text" name="nominal" id="nominal" value="{{ $saldoAwal->nominal }}" onkeypress="return isNumberKey(event)" data-form="1" class="form-control nominal" placeholder="Besar Nominal" autocomplete="off" required >
                        <div class="text-danger" id="warningText"></div>
                    </div>
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

        $('#nominal').keyup();
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

    $(document).on('keyup', '#nominal', function () 
    {
        var nominal = $(this).val().toString();
        var dataForm = $(this).data('form');
        nominal = nominal.replace(/[^\d]/g, "",'');
        $('#nominal').val(toRupiah(nominal));
    });
</script>
@stop