@extends('adminlte::page')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h4>{{ $title }}</h4>
        </div>
        <div class="col-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Jurnal</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.Select2', true)

@section('css')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
        integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form
                action="{{ route('jurnal-update', [$jurnals->first()->id]) }}?serial_number={{ $request->serial_number }}&from={{ $request->from }}&to={{ $request->to }}"
                method="post">
                @csrf
                @method('put')
                @foreach ($jurnals as $jurnal)
                    <div class="row">
                        {{-- <div class="col-md-6 form-group">
                        <label>Nomor</label>
                        <input type="text" name="nomer[{{ $jurnal->id }}]" id="nomer" value="{{ $jurnal->nomer }}" class="form-control" readonly>
                    </div> --}}
                        <div class="col-md-6 form-group">
                            <label>Tanggal Transaksi</label>
                            <input class="form-control datepicker" placeholder="dd-mm-yyyy" id="tgl_transaksi"
                                name="tgl_transaksi[{{ $jurnal->id }}]"
                                value="{{ Carbon\Carbon::createFromFormat('Y-m-d', $jurnal->tgl_transaksi)->format('d-m-Y') }}"
                                autocomplete="off" />
                        </div>
                        @if ($jurnal->anggotaData)
                            <div class="col-md-6 form-group">
                                <label>Anggota</label>
                                <input type="text" name="anggota[{{ $jurnal->id }}]" id="anggota"
                                    value="{{ $jurnal->anggotaData->nama_anggota }}" class="form-control" readonly>
                            </div>
                        @endif
                        {{-- <div class="col-md-6 form-group">
                        <label>Trans Id</label>
                        <input type="text" name="trans_id[{{ $jurnal->id }}]" id="trans_id" value="{{ $jurnal->trans_id }}" class="form-control" readonly>
                    </div> --}}
                        {{-- <div class="col-md-6 form-group">
                            <label>Jumlah Debet Kredit</label>
                            <input type="text" name="jumlah[{{ $jurnal->id }}]" id="jumlah"
                                value="{{ $jurnal->debet }}" class="form-control">
                        </div> --}}
                        {{-- <div class="col-md-6 form-group">
                        <label>Debet</label>
                        <input type="text" name="debet[{{ $jurnal->id }}]" id="debet" value="{{ $jurnal->debet }}" class="form-control" >
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Kredit</label>
                        <input type="text" name="kredit[{{ $jurnal->id }}]" id="kredit" value="{{ $jurnal->kredit }}" class="form-control" >
                    </div> --}}
                        {{-- <div class="col-md-6 form-group">
                            <label>Keterangan</label>
                            <input type="text" name="keterangan[{{ $jurnal->id }}]" id="keterangan"
                                value="{{ $jurnal->keterangan }}" class="form-control" readonly>
                        </div> --}}
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="font-weight-bold">Kredit</h4>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Akun Kredit</label>
                            <select name="akun_kredit[{{ $jurnal->id }}]" id="akun_kredit" class="form-control">
                                @foreach ($coas as $coa)
                                    <option value="{{ $coa->CODE }}" @if ($coa->CODE == $jurnal->akun_kredit) selected @endif>
                                        {{ $coa->CODE . '-' . $coa->NAMA_TRANSAKSI }}</option>
                                @endforeach
                            </select>
                            {{-- <input type="text" name="akun_kredit" id="akun_kredit" value="{{ $jurnal->akun_kredit }}" class="form-control" readonly> --}}
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Kredit</label>
                            <input type='number' step='0.01' name="kredit[{{ $jurnal->id }}]" id="kredit"
                                value="{{ $jurnal->kredit }}" class="form-control">
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12">
                            <h4 class="font-weight-bold">Debet</h4>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Akun Debet</label>
                            <select name="akun_debet[{{ $jurnal->id }}]" id="akun_debet" class="form-control">
                                @foreach ($coas as $coa)
                                    <option value="{{ $coa->CODE }}" @if ($coa->CODE == $jurnal->akun_debet) selected @endif>
                                        {{ $coa->CODE . '-' . $coa->NAMA_TRANSAKSI }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Debet</label>
                            <input type='number' step='0.01' name="debet[{{ $jurnal->id }}]" id="debet"
                                value="{{ $jurnal->debet }}" class="form-control">
                        </div>
                    </div>
                    <hr>
                @endforeach
                <div class="form-group">

                    <button class="btn btn-sm btn-success" type="submit">Submit</button> &nbsp;
                    {{-- <a class="btn btn-sm btn-warning" href="{{ route('jurnal-list') }}">Cancel</a> --}}
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"
        integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function() {
            // initiateDatatables();
            $("#tgl_transaksi").change(function() {
                console.log($('#tgl_transaksi').val());
            });

            $('.datepicker').datepicker({
                format: "dd-mm-yyyy"
            });

            $('input.datepicker').bind('keyup keydown keypress', function(evt) {
                return true;
            });
        });

        $('#akun_kredit').select2();
    </script>
@endsection
