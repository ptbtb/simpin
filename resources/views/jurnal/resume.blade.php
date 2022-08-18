@extends('adminlte::page')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
    <div class="row">
        <div class="col-6"><h4>{{ $title }}</h4></div>
        <div class="col-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Jurnal</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />
    <style>
        .btn-sm{
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <label class="m-0">Filter</label>
        </div>
        <div class="card-body">
            <form action="{{ route('jurnal-list') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Tipe Jurnal</label>
                        {!! Form::select('id_tipe_jurnal', $tipeJurnal, $request->id_tipe_jurnal, ['class' => 'form-control', 'placeholder' => 'All']) !!}
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Nomor</label>
                        <input type="text" name="serial_number" id="serial_number" class="form-control" placeholder="Nomor Transaksi" autocomplete="off" value="{{ old('serial_number', $request->serial_number) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>AKUN</label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="COA" autocomplete="off" value="{{ old('code', $request->code) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Keterangan" autocomplete="off" value="{{ old('keterangan', $request->keterangan) }}">
                    </div>
                    <div class="col-md-3">
                        <label>Dari</label>
                        <input class="form-control datepicker" placeholder="dd-mm-yyyy" id="from" name="from" value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y') }}" autocomplete="off" />
                    </div>
                    <div class="col-md-3">
                        <label>Sampai</label>
                        <input class="form-control datepicker" placeholder="mm-yyyy" id="to" name="to" value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y') }}" autocomplete="off" />
                    </div>
                    <div class="col-md-12 form-group text-center">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-filter"></i> Filter</button>
                        <a href="{{ route('jurnal-export-excel',['id_tipe_jurnal'=>$request->id_tipe_jurnal,'serial_number'=>$request->serial_number,'code'=>$request->code,'from'=>Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y'),'to'=>Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y'),'keterangan'=>$request->keterangan]) }}" class="btn btn-success"><i class="fa fa-download"></i> export Excel</a>
                        <a href="{{ route('jurnal-export-pdf',['id_tipe_jurnal'=>$request->id_tipe_jurnal,'serial_number'=>$request->serial_number,'code'=>$request->code,'from'=>Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y'),'to'=>Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y'),'keterangan'=>$request->keterangan]) }}" class="btn btn-info"><i class="fa fa-download"></i> export PDF</a>
                        <a href="{{ route('jurnal-resume',['from'=>Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y'),'to'=>Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y')]) }}" class="btn btn-info"><i class="fa fa-file"></i> Resume</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Bulan</th>
                    <th>Dr</th>
                    <th>Cr</th>
                    <th>Selisih</th>


                </tr>
                </thead>
                <tbody>
                @foreach($data as $it)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$it['bulan']}}</td>
                        <td>{{number_format($it['Dr'],0,',','.')}}</td>
                        <td>{{number_format($it['Cr'],0,',','.')}}</td>
                        <td>{{number_format($it['Selisih'],0,',','.')}}</td>
                    </tr>

                @endforeach

                </tbody>
                <tfoot>

                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>

@endsection
