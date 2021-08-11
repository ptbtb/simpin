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
                        <label>Periode</label>
                        <input class="form-control datepicker" placeholder="mm-yyyy" id="period" name="period" value="{{ Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y') }}" autocomplete="off" />
                    </div>
                    <div class="col-md-12 form-group text-center">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-filter"></i> Filter</button>
                        <a href="{{ route('jurnal-export-excel',['id_tipe_jurnal'=>$request->id_tipe_jurnal,'serial_number'=>$request->serial_number,'code'=>$request->code,'period'=>Carbon\Carbon::createFromFormat('m-Y', $request->period)->format('m-Y'),'keterangan'=>$request->keterangan]) }}" class="btn btn-success"><i class="fa fa-download"></i> export Excel</a>
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
                        <th>Nomor</th>
                        <th>Tipe Jurnal</th>
                        <th>Akun Debet</th>
                        <th style="width: 10%">Debet</th>
                        <th>Akun Kredit</th>
                        <th style="width: 10%">Kredit</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
    <script>
        $(document).ready(function ()
        {
            initiateDatatables();
             $("#period").change(function(){
                console.log($('#period').val());
                console.log({{ $request->period }});
            });

            $('.datepicker').datepicker({
                format: "mm-yyyy",
                viewMode: "months", 
                minViewMode: "months"
            });

            $('input.datepicker').bind('keyup keydown keypress', function (evt) {
                return false;
            });
        });
        function initiateDatatables()
        {
            $.fn.dataTable.ext.errMode = 'none';
            var table = $('.table').DataTable({
                bProcessing: true,
                bServerSide: true,
                bSortClasses: false,
                ordering: false,
                searching: false,
                responsive: true,
                ajax: {
                    url: '{{ route('jurnal-list-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        @if(isset($request->id_tipe_jurnal)) data.id_tipe_jurnal = '{{ $request->id_tipe_jurnal }}'; @endif
                        
                        var serial_number = '{{ $request->serial_number }}';
                        data.serial_number = serial_number;
                        var code = '{{ $request->code }}';
                        data.code = code;

                        var keterangan = '{{ $request->keterangan }}';
                        data.keterangan = keterangan; 
                        var period = '{{ $request->period }}';
                        data.period = period;
                    }
                },
                aoColumns: [
                    {
                        mData: 'DT_RowIndex', sType: "string",
                        className: "dt-body-center", "name": "DT_RowIndex"
                    },
                    {
                        mData: 'jurnalable_view', sType: "string",
                        className: "dt-body-center", "name": "jurnalable_view",
                        mRender: function(data, type, full)
                        {
                            if(data)
                            {
                                return data.serial_number_view;
                            }
                        }
                    },
                    {
                        mData: 'tipe_jurnal.name', sType: "string",
                        className: "dt-body-center", "name": "tipe_jurnal.name"
                    },
                    {
                        mData: 'akun_debet', sType: "string",
                        className: "dt-body-center", "name": "akun_debet",
                    },
                    {
                        mData: 'debet', sType: "string",
                        className: "dt-body-center", "name": "debet",
                        mRender: function(data, type, full)
                        {
                            var debet = toRupiah(data);
                            return debet;
                        }
                    },
                    {
                        mData: 'akun_kredit', sType: "string",
                        className: "dt-body-center", "name": "akun_kredit"
                    },
                    {
                        mData: 'kredit', sType: "string",
                        className: "dt-body-center", "name": "kredit",
                        mRender: function(data, type, full)
                        {
                            var kredit = toRupiah(data);
                            return kredit;
                        }
                    },
                    {
                        mData: 'keterangan', sType: "string",
                        className: "dt-body-center", "name": "keterangan",
                    },
                    {
                        mData: 'view_created_at', sType: "string",
                        className: "dt-body-center", "name": "view_created_at",
                    },
                    /*{
                        mData: 'created_by.name', sType: "string",
                        className: "dt-body-center", "name": "created_by.name",
                    },*/
                ]
            });

            // add index column
            table.on( 'xhr.dt', function () {
                table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            }).draw();
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
    </script>
@endsection
