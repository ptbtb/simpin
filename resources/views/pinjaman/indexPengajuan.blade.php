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
@section('plugins.Select2', true)

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
            @can('print jkk')
                <a href="{{ route('pengajuan-pinjaman-print-jkk') }}" class="btn btn-sm btn-info"><i class="fas fa-print"></i> Print JKK</a>
            @endcan
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
                        <th>Form Persetujuan</th>
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
                            <td><a class="btn btn-warning btn-sm" href="{{ asset($pengajuan->form_persetujuan) }}" target="_blank"><i class="fa fa-file"></i></a></td>
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
                                @if ($pengajuan->bukti_pembayaran)
                                    <a class="btn btn-warning btn-sm" href="{{ asset($pengajuan->bukti_pembayaran) }}" target="_blank"><i class="fa fa-file"></i></a>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if (Auth::user()->isAnggota())
                                    @if ($pengajuan->menungguKonfirmasi())
                                        <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DIBATALKAN }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Cancel</a>
                                    @else
                                        -
                                    @endif
                                @else    
                                    @can('approve pengajuan pinjaman')
                                        @if ($pengajuan->menungguKonfirmasi())
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_SPV }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Terima</a>
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($pengajuan->menungguApprovalSpv())
                                            @can('approve pengajuan pinjaman spv')
                                                <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_ASMAN }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>
                                                <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                            @endcan
                                        @elseif($pengajuan->menungguApprovalAsman())
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_MANAGER }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($pengajuan->menungguApprovalManager())
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_BENDAHARA }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($pengajuan->menungguApprovalBendahara())
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_KETUA}}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($pengajuan->menungguApprovalKetua())
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN}}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>
                                            <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>
                                        @elseif($pengajuan->menungguPembayaran())
                                            @can('bayar pengajuan pinjaman')
                                                @if ($pengajuan->jkkPrinted())
                                                    <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}" class="text-white btn btn-sm btn-success btn-konfirmasi">Konfirmasi Pembayaran</a>
                                                @else
                                                    JKK Belum di Print
                                                @endif
                                            @endcan
                                            <!-- <b style="color: blue !important"><i class="fas fa-clock"></i></b> -->
                                        @elseif($pengajuan->diterima())
                                            <b style="color: green !important"><i class="fas fa-check"></i></b>
                                        @else
                                            <b style="color: red !important"><i class="fas fa-times"></i></b>
                                        @endif

                                        @if($pengajuan->menungguKonfirmasi() || $pengajuan->menungguApprovalSpv() || $pengajuan->menungguApprovalAsman() || $pengajuan->menungguApprovalManager() || $pengajuan->menungguApprovalBendahara() || $pengajuan->menungguApprovalKetua())
                                        <a data-id="{{ $pengajuan->kode_pengajuan }}" data-code="{{ $pengajuan->kode_jenis_pinjam }}" data-nominal="{{ $pengajuan->besar_pinjam }}" class="text-white btn btn-sm btn-info btn-jurnal"><i class="fas fa-eye"></i> Jurnal</a>
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
                    <div class="form-detail"></div>
                    <hr>
                    <form enctype="multipart/form-data" id="formKonfirmasi">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Jenis Akun</label>
                                <select name="jenis_akun" id="jenisAkun" class="form-control select2" required>
                                    <option value="1">KAS</option>
                                    <option value="2" selected>BANK</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Upload Bukti Pembayaran</label>
                                <input type="file" name="bukti_pembayaran" id="buktiPembayaran" class="form-control" required>
                                {{-- <div class="custom-file">
									<input type="file" class="custom-file-input" id="buktiPembayaran" name="bukti_pembayaran" style="cursor: pointer">
									<label class="custom-file-label" for="customFile">Choose File</label>
								</div> --}}
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Akun</label>
                                <select name="id_akun_debet" id="code" class="form-control select2" required>
                                    <option value="" selected disabled>Pilih Akun</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    @if (isset($pengajuan))
                        <a data-id="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}" class="text-white btn btn-sm btn-success btn-approval">Bayar</a>
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
            var url = '{{ route("pengajuan-pinjaman-update-status") }}';

            var files = $('#buktiPembayaran')[0].files;
            var id_akun_debet = $('#code').val();

            // files is mandatory when status pengajuan pinjaman diterima
            if(status == {{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }} && files[0] == undefined)
            {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Wajib upload bukti pembayaran!',
                });
            }
            else
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
                        var formData = new FormData();
                        var token = "{{ csrf_token() }}";
                        formData.append('_token', token);
                        formData.append('id', id);
                        formData.append('status', status);
                        formData.append('bukti_pembayaran', files[0]);
                        formData.append('password', password);
                        formData.append('id_akun_debet', id_akun_debet);
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
            }
        });

        $('.btn-konfirmasi').on('click', function ()
        {
            var id = $(this).data('id');
            var action = $(this).data('action');
            var url = baseURL + '/pinjaman/detail-pembayaran/'+id;
            
            $.get(url, function( data ) {
                $('#my-modal .form-detail').html(data);
                $('#my-modal').modal({
                    backdrop: false 
                });
                $('#my-modal').modal('show');
            });

            $('#jenisAkun').trigger( "change" );
        });

        $('.btn-jurnal').on('click', function ()
        {
            var id = $(this).data('id');
            var code = $(this).data('code');
            var nominal = $(this).data('nominal');

            var htmlText =  '<div class="container-fluid">'+
                                '<div class="row">'+
                                    '<div class="col-md-6 offset-md-3" style="font-size:15px">'+
                                        '<table class="table">'+
                                            '<thead class="thead-dark">'+
                                                '<tr>'+
                                                    '<th>Akun Kredit</th>'+
                                                    '<th>Kredit</th>'+
                                                    '<th>Akun Debet</th>'+
                                                    '<th>Debet</th>'+
                                                '</tr>'+
                                            '</thead>'+
                                            '<tbody>'+
                                                '<tr>'+
                                                    '<td>'+code+'</td>'+
                                                    '<td>'+new Intl.NumberFormat(['ban', 'id']).format(nominal)+'</td>'+
                                                    '<td>Bank/Kas</td>'+
                                                    '<td>'+new Intl.NumberFormat(['ban', 'id']).format(nominal)+'</td>'+
                                                '</tr>'+
                                            '</tbody>'+
                                        '</table>'+
                                    '</div>'+
                                '</div>'+
                            '</div>';

            Swal.fire({
                title: 'Jurnal Pengajuan',
                html: htmlText, 
                icon: "info",
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#00a65a",
                grow: 'row',
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
            }).then((result) => {
                if (result.value) {
                }
            });
        });

        $(".select2").select2({
            width: '100%',
        });

        // code array
        var bankAccountArray = [];

        // get bank account number from php
        @foreach($bankAccounts as $key => $bankAccount)
            bankAccountArray[{{ $loop->index }}]={ id : {{ $bankAccount->id }}, code: '{{ $bankAccount->CODE }}', name: '{{ $bankAccount->NAMA_TRANSAKSI }}' };
        @endforeach
        
        // trigger to get kas or bank select option
        $(document).on('change', '#jenisAkun', function () 
        {
            // remove all option in code
            $('#code').empty();

            // get jenis akun
            var jenisAkun = $('#jenisAkun').val();

            if(jenisAkun == 2)
            {
                // loop through code bank
                $.each(bankAccountArray, function(key, bankAccount) 
                {
                    // set dafault to 102.18.000
                    if(bankAccount.id == 22)
                    {
                        var selected = 'selected';
                    }
                    else
                    {
                        var selected = '';
                    }
                    
                    // insert new option
                    $('#code').append('<option value="'+bankAccount.id+'"'+ selected +'>'+bankAccount.code+ ' ' + bankAccount.name + '</option>');
                });
            }
            else if(jenisAkun == 1)
            {
                // insert new option 
                $('#code').append('<option value="4" >101.01.102 KAS SIMPAN PINJAM</option>');
            }

            $('#code').trigger( "change" );
        });

    </script>
@endsection