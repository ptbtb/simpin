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
    {{-- <div class="card">
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
    </div> --}}
    <div class="card">
        <div class="card-header text-right">
            {{-- <a href="{{ route('pinjaman-download-pdf', ['from' => $request->from, 'to' => $request->to, 'status' => 'belum lunas']) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
            <a href="{{ route('pinjaman-download-excel', ['from' => $request->from, 'to' => $request->to, 'status' => 'belum lunas']) }}" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download Excel</a> --}}
            <a href="{{ route('pengajuan-pinjaman-add') }}" class="btn btn-sm btn-success"><i class="fas fa-plus"></i> Buat Pengajuan Pinjaman</a>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Nama Anggota</th>
                        <th>Jenis Pinjaman</th>
                        <th>Besar Pinjaman</th>
                        <th>Status</th>
                        <th>Tanggal Acc</th>
                        <th>Diajukan Oleh</th>
                        <th>Dikonfirmasi Oleh</th>
                        <th>Pembayaran Oleh</th>
                        <th style="width: 20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listPengajuanPinjaman as $pengajuan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $pengajuan->tgl_pengajuan->format('d M Y') }}</td>
                            <td>
                                @if ($pengajuan->anggota)
                                    {{ $pengajuan->anggota->nama_anggota }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ ucwords(strtolower($pengajuan->jenisPinjaman->nama_pinjaman)) }}</td>
                            <td>Rp. {{ number_format($pengajuan->besar_pinjam,0,",",".") }}</td>
                            <td class="str-to">{{ ucfirst($pengajuan->statusPengajuan->name) }}</td>
                            <td>
                                @if ($pengajuan->tgl_acc)
                                    {{ $pengajuan->tgl_acc->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($pengajuan->createdBy)
                                    {{ $pengajuan->createdBy->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($pengajuan->approvedBy)
                                    {{ $pengajuan->approvedBy->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($pengajuan->paidByCashier)
                                    {{ $pengajuan->paidByCashier->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if (Auth::user()->isAnggota())
                                    @if ($pengajuan->menungguKonfirmasi())
                                        <a data-id="{{ $pengajuan->kode_pengajuan }}" data-action="{{ CANCEL_PENGAJUAN_PINJAMAN }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Cancel</a>
                                    @else
                                        -
                                    @endif
                                @else    
                                    @can('approve pengajuan pinjaman')
                                        @if ($pengajuan->menungguKonfirmasi())
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-action="{{ APPROVE_PENGAJUAN_PINJAMAN }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Terima</a>
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-action="{{ REJECT_PENGAJUAN_PINJAMAN }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($pengajuan->menungguPembayaran())
                                            <a data-id="{{ $pengajuan->pinjaman->kode_pinjam }}" data-action="{{ KONFIRMASI_PEMBAYARAN_PENGAJUAN_PINJAMAN }}" class="text-white btn btn-sm btn-success btn-konfirmasi">Konfirmasi Pembayaran</a>
                                            <!-- <b style="color: blue !important"><i class="fas fa-clock"></i></b> -->
                                        @elseif($pengajuan->diterima())
                                            <b style="color: green !important"><i class="fas fa-check"></i></b>
                                        @else
                                            <b style="color: red !important"><i class="fas fa-times"></i></b>
                                        @endif
                                    @endcan
                                @endif
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
                    <a data-id="{{ $pengajuan->kode_pengajuan }}" data-action="{{ KONFIRMASI_PEMBAYARAN_PENGAJUAN_PINJAMAN }}" class="text-white btn btn-sm btn-success btn-approval">Bayar</a>
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
        $.fn.modal.Constructor.prototype._enforceFocus = function() {};
        $('#from').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
        $('#to').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
        $('.table').DataTable();

        $('.btn-approval').on('click', function ()
        {
            var id = $(this).data('id');
            var action = $(this).data('action');
            var url = '{{ route("pengajuan-pinjaman-update-status") }}';
            
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
                    $.ajax({
                        type: 'get',
                        url: url,
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "id": id,
                            "action": action,
                            "password": password
                    },                        
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
        });

        $('.btn-konfirmasi').on('click', function ()
        {
            var id = $(this).data('id');
            var action = $(this).data('action');
            var url = baseURL + '/pinjaman/detail-pembayaran/'+id;
            
            $.get(url, function( data ) {
                $('#my-modal .modal-body').html(data);
                $('#my-modal').modal({
                    backdrop: false 
                });
                $('#my-modal').modal('show');
            });
        });
    </script>
@endsection