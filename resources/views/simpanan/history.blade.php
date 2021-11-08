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
            <li class="breadcrumb-item"><a href="">Simpanan</a></li>
			<li class="breadcrumb-item active">History Simpanan</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Datatables', true)

@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .btn-sm{
            font-size: .8rem;
        }

        .box-custom{
            border: 1px solid black;
            border-radius: 0;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <label class="m-0">Filter</label>
        </div>
        <div class="card-body">
            <form action="{{ route('simpanan-history') }}" method="post">
                @csrf
                <input type="hidden" name="status" value="lunas">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <label>From</label>
                        <input id="from" type="text" name="from" class="form-control" placeholder="yyyy-mm-dd" value="{{ ($request->from)? $request->from:'' }}">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>To</label>
                        <input id="to" type="text" name="to" class="form-control" placeholder="yyyy-mm-dd" value="{{ ($request->to)? $request->to:'' }}">
                    </div>
                    <div class="col-md-1 form-group" style="margin-top: 26px">
                        <button type="submit" class="btn btn-sm btn-success form-control"><i class="fa fa-filter"></i> Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header text-right">
            <a href="{{ route('simpanan-download-pdf', ['from' => $request->from, 'to' => $request->to, 'status' => 'lunas']) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
            <a href="{{ route('simpanan-download-excel', ['from' => $request->from, 'to' => $request->to, 'status' => 'lunas']) }}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download Excel</a>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        @if (\Auth::user()->roles()->first()->id != ROLE_ANGGOTA)
                            <th>Kode Simpanan</th>
                            <th>Nama Anggota</th>
                            <th>Nomor Anggota</th>
                        @endif
                        <th>Jenis Simpanan</th>
                        <th>Besar Simpanan</th>
                        <!-- <th>Tanggal Mulai</th> -->
                        <th>Tanggal Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                </tbody> 
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script>
        var baseURL = {!! json_encode(url('/')) !!};
        $('#from').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
        $('#to').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
        var t = $('.table').DataTable({
            processing: true,
            serverside: true,
            responsive: true,
            // order: [[ 6, "desc" ]],
            ajax: {
                url: '{{ route('simpanan-history-data') }}',
                dataSrc: 'data',
                data: function(data){
                    @if(isset($request->from)) data.from = '{{ $request->from }}'; @endif
                    @if(isset($request->to)) data.to = '{{ $request->to }}'; @endif
                }
            },
            aoColumns: [
                { 
                    mData: null		
                },
                @if (\Auth::user()->roles()->first()->id != ROLE_ANGGOTA)
                { 
                    mData: 'kode_simpan', sType: "string", 
                    className: "dt-body-center", "name": "kode_simpan"				
                },
                { 
                    mData: 'anggota.nama_anggota', sType: "string", 
                    className: "dt-body-center", "name": "anggota.nama_anggota"	,
                    mRender: function (data, type, full) {
                        if (data == null || data == '') {
                            return '-';
                        }
                        return data;
                    }			
                },
                { 
                    mData: 'anggota.kode_anggota', sType: "string", 
                    className: "dt-body-center", "name": "anggota.kode_anggota"	,
                    mRender: function (data, type, full) {
                        if (data == null || data == '') {
                            return '-';
                        }
                        return data;
                    }			
                },
                @endif
                { 
                    mData: 'jenis_simpan', sType: "string", 
                    className: "dt-body-center", "name": "jenis_simpan"	,
                    mRender: function (data, type, full) {
                        if (data == null || data == '') {
                            return '-';
                        }
                        return data;
                    }			
                },
                { 
                    mData: 'besar_simpanan_rupiah', sType: "string", 
                    className: "dt-body-center", "name": "besar_simpanan_rupiah"	,
                    mRender: function (data, type, full) {
                        if (data == null || data == '') {
                            return '-';
                        }
                        return data;
                    }			
                },
                // { 
                //     mData: 'tanggal_mulai', sType: "string", 
                //     className: "dt-body-center", "name": "tanggal_mulai"	,
                //     mRender: function (data, type, full) {
                //         if (data == null || data == '') {
                //             return '-';
                //         }
                //         return data;
                //     }			
                // },
                { 
                    mData: 'tanggal_entri', sType: "string", 
                    className: "dt-body-center", "name": "tanggal_entri"	,
                    mRender: function (data, type, full) {
                        if (data == null || data == '') {
                            return '-';
                        }
                        return data;
                    }			
                },
            ]
        });

        t.on( 'order.dt search.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
    </script>
@endsection