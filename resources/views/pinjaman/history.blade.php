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
            <form action="{{ route('pinjaman-history') }}" method="post">
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
            <a href="{{ route('pinjaman-download-pdf', ['from' => $request->from, 'to' => $request->to, 'status' => 'lunas']) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
            <a href="{{ route('pinjaman-download-excel', ['from' => $request->from, 'to' => $request->to, 'status' => 'lunas']) }}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download Excel</a>
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
                        <th>Tanggal Pinjaman</th>
                        <th>Jenis Pinjaman</th>
                        <th>Besar Pinjaman</th>
                        <th>Sisa Pinjaman</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th>#</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listPinjaman as $pinjaman)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            @if (\Auth::user()->roles()->first()->id != ROLE_ANGGOTA)
                                <td>
                                    @if ($pinjaman->anggota)
                                        {{ $pinjaman->anggota->nama_anggota }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($pinjaman->anggota)
                                        {{ $pinjaman->anggota->kode_anggota }}
                                    @else
                                        -
                                    @endif
                                </td>
                            @endif
                            <td>{{ $pinjaman->tgl_entri->format('d M Y') }}</td>
                            <td>{{ $pinjaman->jenisPinjaman->nama_pinjaman }}</td>
                            <td>Rp. {{ number_format($pinjaman->besar_pinjam,0,",",".") }}</td>
                            <td>Rp. {{ number_format($pinjaman->sisa_pinjaman,0,",",".") }}</td>
                            <td>{{ $pinjaman->tgl_tempo->format('d M Y') }}</td>
                            <td>{{ ucwords($pinjaman->status) }}</td>
                            <td>
                                <a data-id="{{ $pinjaman->kode_pinjam }}" class="btn btn-sm btn-info text-white"><i class="fa fa-eye"></i> Detail</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody> 
            </table>
        </div>
    </div>

    <div id="my-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Detail Pinjaman</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
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

        $('.table').on('click', 'a', function ()
        {
            var data_id = $(this).data('id');
            $.get(baseURL + "/pinjaman/detail/" + data_id, function( data ) {
                $('#my-modal .modal-body').html(data);
                $('#my-modal').modal({
                    backdrop: false 
                });
                $('#my-modal').modal('show');
            });
        });
    </script>
@endsection