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
			<li class="breadcrumb-item active">List Pinjaman</li>
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
            <form action="{{ route('pinjaman-list') }}" method="post">
                @csrf
                <input type="hidden" name="status" value="belum lunas">
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
            <a href="{{ route('pinjaman-download-pdf', ['from' => $request->from, 'to' => $request->to, 'status' => STATUS_PINJAMAN_BELUM_LUNAS]) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
            <a href="{{ route('pinjaman-download-excel', ['from' => $request->from, 'to' => $request->to, 'status' => STATUS_PINJAMAN_BELUM_LUNAS]) }}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download Excel</a>
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
                        <th style="width: 15%">#</th>
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
                            <td>
                                @if ($pinjaman->jenisPinjaman)
                                    {{ $pinjaman->jenisPinjaman->nama_pinjaman }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>Rp. {{ number_format($pinjaman->besar_pinjam,0,",",".") }}</td>
                            <td>Rp. {{ number_format($pinjaman->sisa_pinjaman,0,",",".") }}</td>
                            <td>{{ $pinjaman->tgl_tempo->format('d M Y') }}</td>
                            <td>{{ ucwords($pinjaman->statusPinjaman->name) }}</td>
                            <td>
                                <a href="{{ route('pinjaman-detail', ['id'=>$pinjaman->kode_pinjam]) }}" class="btn btn-sm btn-info text-white" data-action='detail'><i class="fa fa-eye"></i> Detail</a>
                                @can('delete pinjaman')
                                    <a class="btn btn-sm btn-danger text-white btn-delete" style="cursor: pointer" data-action='delete' data-id='{{ $pinjaman->kode_pinjam }}' data-token='{{ csrf_token() }}'><i class="fa fa-trash"></i> Delete</a>
                                @endcan
                                {{-- <a data-id="{{ $pinjaman->kode_pinjam }}" class="btn btn-sm btn-info text-white"><i class="fa fa-eye"></i> Detail</a> --}}
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
            var data_action = $(this).data('action');
            var url = baseURL+'/pinjaman/delete/'+data_id;
            var token = $(this).data('token');
            if (data_action == 'delete')
            {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    input: 'password',
                    inputAttributes: {
                        name: 'password',
                        placeholder: 'Password',
                        required: 'required',
                    },
                    validationMessage:'Password required',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var password = result.value;
                        url = url + '?pw=' + password
                        $.ajax({
                            url: url,
                            type: 'delete',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            contentType: false,
                            processData: false,                     
                            success: function(data) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Your has been changed',
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            },
                            error: function(error){
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: error.responseJSON.message,
                                    showConfirmButton: true
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        location.reload();
                                    }
                                });
                            }
                        });
                    }
                });
            }
        });
    </script>
@endsection