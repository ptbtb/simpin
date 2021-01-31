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
            <li class="breadcrumb-item"><a href="">Penarikan</a></li>
			<li class="breadcrumb-item active">List Penarikan</li>
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
        <div class="card-header text-right">
            @can('print jkk penarikan')
                <a href="{{ route('penarikan-print-jkk') }}" class="btn btn-sm btn-info"><i class="fas fa-print"></i> Print JKK</a>
            @endcan
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal Penarikan</th>
                        <th>Nama Anggota</th>
                        <th>Besar Penarikan</th>
                        <th>Status</th>
                        <th>Tanggal Acc</th>
                        <th>Diajukan Oleh</th>
                        <th>Dikonfirmasi Oleh</th>
                        <th>Pembayaran Oleh</th>
                        <th>Bukti Pembayaran</th>
                        <th style="width: 20%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listPenarikan as $penarikan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $penarikan->tgl_ambil->format('d M Y') }}</td>
                            <td>{{ $penarikan->anggota->nama_anggota }}</td>
                            <td>{{ "Rp ". number_format($penarikan->besar_ambil,0,",",".") }}</td>
                            <td>{{ $penarikan->statusPenarikan->name }}</td>
                            <td>
                                @if ($penarikan->tgl_acc)
                                    {{ $penarikan->tgl_acc->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                {{ $penarikan->createdBy->name }}
                            </td>
                            <td>
                                @if ($penarikan->approvedBy)
                                    {{ $penarikan->approvedBy->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($penarikan->paidByCashier)
                                    {{ $penarikan->paidByCashier->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($penarikan->bukti_pembayaran)
                                    <a class="btn btn-warning btn-sm" href="{{ asset($penarikan->bukti_pembayaran) }}" target="_blank"><i class="fa fa-file"></i></a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if (Auth::user()->isAnggota())
                                    @if ($penarikan->menungguKonfirmasi())
                                        <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_DIBATALKAN }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Cancel</a>
                                    @else
                                        -
                                    @endif
                                @else    
                                    @can('approve penarikan')
                                        @if ($penarikan->menungguKonfirmasi())
                                            <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_SPV }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Terima</a>
                                            <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($penarikan->menungguApprovalSpv())
                                            @can('approve penarikan spv')
                                                <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_ASMAN }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>
                                                <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                            @endcan
                                        @elseif($penarikan->menungguApprovalAsman())
                                            <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_MANAGER }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>
                                            <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($penarikan->menungguApprovalManager())
                                            <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_BENDAHARA }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>
                                            <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($penarikan->menungguApprovalBendahara())
                                            <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_KETUA}}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>
                                            <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($penarikan->menungguApprovalKetua())
                                            <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN}}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>
                                            <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($penarikan->menungguPembayaran())
                                            @can('bayar pengajuan pinjaman')
                                                @if ($penarikan->jkkPrinted())
                                                    <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_DITERIMA }}" class="text-white btn btn-sm btn-success btn-konfirmasi">Konfirmasi Pembayaran</a>
                                                @else
                                                    JKK Belum di Print
                                                @endif
                                            @endcan
                                            <!-- <b style="color: blue !important"><i class="fas fa-clock"></i></b> -->
                                        @elseif($penarikan->diterima())
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
                    <h5>Detail Penarikan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-detail"></div>
                    <hr>
                    <form enctype="multipart/form-data" id="formKonfirmasi">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Upload Bukti Pembayaran</label>
                                <input type="file" name="bukti_pembayaran" id="buktiPembayaran" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    @if (isset($penarikan))
                        <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_DITERIMA }}" class="text-white btn btn-sm btn-success btn-approval">Bayar</a>
                    @endif
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
            var status = $(this).data('status');
            var url = '{{ route("penarikan-update-status") }}';

            var files = $('#buktiPembayaran')[0].files;
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
                    var formData = new FormData();
                    var token = "{{ csrf_token() }}";
                    formData.append('_token', token);
                    formData.append('id', id);
                    formData.append('status', status);
                    formData.append('bukti_pembayaran', files[0]);
                    formData.append('password', password);
                    $.ajax({
                        type: 'post',
                        url: url,
                        data: formData,   
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
        });

        $('.btn-konfirmasi').on('click', function ()
        {
            var id = $(this).data('id');
            var action = $(this).data('action');
            var url = baseURL + '/penarikan/detail-transfer/'+id;
            
            $.get(url, function( data ) {
                $('#my-modal .form-detail').html(data);
                $('#my-modal').modal({
                    backdrop: false 
                });
                $('#my-modal').modal('show');
            });
        });
    </script>
@endsection