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
            <li class="breadcrumb-item"><a href="">Pinjaman</a></li>
			<li class="breadcrumb-item active">History Pinjaman</li>
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
                            <th>Nama Anggota</th>
                            <th>Nomor Anggota</th>
                        @endif
                        <th>Jenis Simpanan</th>
                        <th>Besar Simpanan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Entry</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listSimpanan as $simpanan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            @if (\Auth::user()->roles()->first()->id != ROLE_ANGGOTA)
                                <td>
                                    @if ($simpanan->anggota)
                                        {{ $simpanan->anggota->nama_anggota }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($simpanan->anggota)
                                        {{ $simpanan->anggota->kode_anggota }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                            <td>{{ $simpanan->jenis_simpan }}</td>
                            <td>Rp. {{ number_format($simpanan->besar_simpanan,0,",",".") }}</td>
                            <td>
                                @if ($simpanan->tgl_mulai)
                                    {{ $simpanan->tgl_mulai->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($simpanan->tgl_entri)
                                    {{ $simpanan->tgl_entri->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
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
        $('.table').DataTable();
    </script>
@endsection