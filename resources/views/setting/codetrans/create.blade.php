@extends('adminlte::page')

@section('title', 'Add Kode Transaksi')
@section('plugins.Datatables', true)
@section('content_header')
<h1>Add Kode Transaksi</h1>
@stop

@section('plugins.Select2', true)

@section('content')
<div class="card">
    <!-- /.card-header -->
    <div class="card-body">
        <form action="{{ route('kode-transaksi-store') }}" method="post" id="form" enctype="multipart/form-data">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-panel">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label for="normalBalance">Normal Balance</label>
                            <br>
                            <select name="normal_balance" id="normalBalance" class="form-control select2Akun" required>
                            @foreach ($normalBalances as $normalBalance)
                                <option value="{{ $normalBalance->id }}">{{ $normalBalance->name }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tipeAkun">Tipe</label>
                            <br>
                            <select name="code_type" id="tipeAkun" class="form-control select2Akun" required>
                            @foreach ($codeTypes as $codeType)
                                <option value="{{ $codeType->id }}">{{ $codeType->name }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="codeCategory">Kategori</label>
                            <br>
                            <select name="code_category" id="codeCategory" class="form-control select2Akun" required>
                            @foreach ($codeCategories as $codeCategory)
                                <option value="{{ $codeCategory->id }}">{{ $codeCategory->name }}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="induk">Induk</label>
                            <br>
                            <select name="induk" id="induk" class="form-control select2Akun" required>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->CODE }}">{{ $parent->NAMA_TRANSAKSI }}</option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-panel">
                        <div class="form-group">
                            <label for="code">Kode</label>
                            <input type="text" name="code" id="code" class="form-control" placeholder="10.011.000" autocomplete="off" required maxlength="12">
                        </div>
                        <div class="form-group">
                            <label for="namaTransaksi">Nama Transaksi</label>
                            <input type="text" name="nama_transaksi" id="namaTransaksi" class="form-control" placeholder="KAS" autocomplete="off" required maxlength="45">
                        </div>
                        <div class="form-group">
                            <label for="isParent">Summary</label>
                            <br>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="kode_summary" id="kode_summary_induk" value="1" checked>
                                <label class="form-check-label" for="kode_summary_induk">
                                    Induk
                                </label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="kode_summary" id="kode_summary_anak" value="0">
                                <label class="form-check-label" for="kode_summary_anak">
                                    Anak
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <button class="btn btn-info"><span class='glyphicon glyphicon-pencil'></span> Submit</button>
                </div>
            </div>
        </form>
    </div>

</div><!-- /row -->
@stop

@section('css')
@stop

@section('js')
<script>
    $(document).ready(function () {
        initiateSelect2();
    });
    
    function initiateSelect2()
    {
        $(".select2Akun").select2({
            width: '100%',
        });
    }
</script>
@stop