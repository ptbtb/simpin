@extends('adminlte::page')

@section('title', 'Edit Kode Transaksi')
@section('plugins.Datatables', true)
@section('content_header')
    <h1>Edit Kode Transaksi</h1>
@stop

@section('plugins.Select2', true)

@section('content')
    <div class="card">
        <!-- /.card-header -->
        <div class="card-body">
            <form action="{{ route('kode-transaksi-update',[$codes->id]) }}" method="post" id="form" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-panel">
                            {{csrf_field()}}
                            <div class="form-group">
                                <label for="normalBalance">Normal Balance</label>
                                <br>
                                {!! Form::select('normal_balance', $normalBalances,$codes->normal_balance_id,['id' => 'normalBalance', 'class' => 'form-control']) !!}

                            </div>
                            <div class="form-group">
                                <label for="tipeAkun">Tipe</label>
                                <br>
                                {!! Form::select('code_type', $codeTypes,$codes->code_type_id,['id' => 'tipeAkun', 'class' => 'form-control'] )!!}

                            </div>
                            <div class="form-group">
                                <label for="codeCategory">Kategori</label>
                                <br>
                                {!! Form::select('code_category', $codeCategories,$codes->code_category_id,['id' => 'codeCategory', 'class' => 'form-control']) !!}
                            </div>
                            <div class="form-group">
                                <label for="induk">Induk</label>
                                <br>
                                {!! Form::select('induk', $parents,$codes->induk_id,['id' => 'induk', 'class' => 'form-control', 'placeholder' => ' ']) !!}
                                {{-- <select name="induk" id="induk" class="form-control select2Akun" required>
                                    <option value=""></option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->CODE }}">{{ $parent->NAMA_TRANSAKSI }}</option>
                                @endforeach
                                </select> --}}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-panel">
                            <div class="form-group">
                                <label for="code">Kode</label>
                                {!! Form::text('code', $codes->CODE, ['id' => 'code', 'class' => 'form-control',  'required']) !!}
                            </div>
                            <div class="form-group">
                                <label for="namaTransaksi">Nama Transaksi</label>
                                {!! Form::text('nama_transaksi', $codes->NAMA_TRANSAKSI, ['id' => 'namaTransaksi', 'class' => 'form-control',  'required']) !!}
                            </div>
                            <div class="form-group">
                                <label for="isParent">Summary</label>
                                <br>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kode_summary" id="kode_summary_induk" value="1" @if($codes->is_parent==1) checked @endif>
                                    <label class="form-check-label" for="kode_summary_induk">
                                        Induk
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="kode_summary" id="kode_summary_anak" value="0" @if($codes->is_parent==0) checked @endif>
                                    <label class="form-check-label" for="kode_summary_anak">
                                        Anak
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="active">Active</label>
                                <br>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="active" id="active" value="1" @if($codes->active==1) checked @endif>
                                    <label class="form-check-label" for="induk">
                                        Active
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
