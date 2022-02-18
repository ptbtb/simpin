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
    <link href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css" rel="stylesheet" />
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
        <div class="card-header text-left">
            <label class="m-0">Filter</label>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="form-group col-md-4">
                    <label>Status</label>
                    <select name="status_pengajuan" class="form-control input" id="select_status_pengajuan">
                        <option value="" selected>All</option>
                        @foreach ($statusPengajuans as $statusPengajuan)
                            <option value="{{ $statusPengajuan->id }}">{{ $statusPengajuan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Tgl. Pengajuan</label>
                    <input type="text" name="start_tgl_pengajuan" id="start_input_tgl_pengajuan" value="{{ old('start_tgl_pengajuan') }}" class="form-control" placeholder="Start date" autocomplete="off">
                    <input type="text" name="end_tgl_pengajuan" id="end_input_tgl_pengajuan" value="{{ old('end_tgl_pengajuan') }}" class="form-control" placeholder="End Date" autocomplete="off">
                </div>
                <div class="form-group col-md-4">
                    <label>Anggota</label>
                    <select name="anggota" class="form-control select2" id="select_anggota">
                        <option value="" selected>All</option>
                        @foreach ($anggotas as $anggota)
                            <option value="{{ $anggota->kode_anggota }}">{{ $anggota->nama_anggota }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 form-group" style="margin-top: 26px">
                    <a class="btn btn-sm btn-success form-control" id="btnFiterSubmitSearch" style="color:white; padding-top:8px"><i class="fa fa-filter"></i> Filter</a>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header text-right">
            {{-- <a href="{{ route('pinjaman-download-pdf', ['from' => $request->from, 'to' => $request->to, 'status' => 'belum lunas']) }}" class="btn mt-1 btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a> --}}
            @can('print jkk')
                <a href="{{ route('pengajuan-pinjaman-print-jkk') }}" class="btn mt-1 btn-sm btn-info"><i class="fas fa-print"></i> Print JKK</a>
                {{-- <a href="{{ route('download-pengajuan-pinjaman-excel', $request->toArray()) }}" class="btn mt-1 btn-sm btn-warning"><i class="fa fa-download"></i> Download Excel</a> --}}
            @endcan
            @can('download pengajuan pinjaman')
                <a href="#" class="btn mt-1 btn-sm btn-warning btn-download-excel"><i class="fa fa-download"></i> Download Excel</a>
            @endcan
            <a href="{{ route('pengajuan-pinjaman-add') }}" class="btn mt-1 btn-sm btn-success"><i class="fas fa-plus"></i> Buat Pengajuan Pinjaman</a>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped" id="pengajuan-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>#</th>
                        <th>No</th>
                        <th>No JKK</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Nomer Anggota</th>
                        <th>Nama Anggota</th>
                        <th>Jenis Pinjaman</th>
                        <th>Besar Pinjaman</th>
                        <th>Form Persetujuan</th>
                        <th>Status</th>
                        <th>Tanggal Acc</th>
                        <th>Diajukan Oleh</th>
                        <th>Dikonfirmasi Oleh</th>
                        <th>Keterangan</th>
                        <th>Pembayaran Oleh</th>
                        <th style="width: 20%">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="edit-coa-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Rubah Coa KAS/BANK</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-detail"></div>
                    <hr>
                    <form enctype="multipart/form-data" id="formKonfirmasi">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>COA LAMA</label>
                                <input id="coa_lama" type="text" name="coa_lama" class="form-control" readonly>
                                <input id="id_jurnal" type="hidden" name="id" class="form-control" readonly>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jenis Akun (COA BARU)</label>
                                <select name="jenis_akun" id="jenisAkun" class="form-control select2" required>
                                    <option value="1">KAS</option>
                                    <option value="2" selected>BANK</option>
                                    <option value="3" selected>R/K</option>
                                </select>
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

                        <a data-id=""class="text-white btn mt-1 btn-sm btn-success btn-editcoa">update</a>

                    <button type="button" class="btn mt-1 btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
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
                                <label>Tanggal Pembayaran</label>
                                <input id="tgl_transaksi" type="date" name="tgl_transaksi" class="form-control" placeholder="yyyy-mm-dd" required value="{{ Carbon\Carbon::today()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jenis Akun</label>
                                <select name="jenis_akun" id="jenisAkun2" class="form-control select2" required>
                                    <option value="1">KAS</option>
                                    <option value="2" selected>BANK</option>
                                    <option value="3">R/K</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Upload Bukti Pembayaran</label>
                                <input type="file" name="bukti_pembayaran" id="buktiPembayaran" class="form-control">
                                {{-- <div class="custom-file">
									<input type="file" class="custom-file-input" id="buktiPembayaran" name="bukti_pembayaran" style="cursor: pointer">
									<label class="custom-file-label" for="customFile">Choose File</label>
								</div> --}}
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Akun</label>
                                <select name="id_akun_debet" id="code2" class="form-control select2" required>
                                    <option value="" selected disabled>Pilih Akun</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">

                        <a data-id="" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN }}"class="text-white btn mt-1 btn-sm btn-success btn-approval">Bayar</a>

                    <button type="button" class="btn mt-1 btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loading" style="position: absolute; top: 0; left: 0; z-index: 100; height: 100%; width: 100%; background-color: rgba(0, 0, 0, 0.5)">
        <div class="d-flex w-100 h-100">
            <img src="{{ asset('img/load.gif') }}" class="m-auto">
        </div>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script>
        var baseURL = {!! json_encode(url('/')) !!};
        $.fn.dataTable.ext.errMode = 'none';

        var table = $('#pengajuan-table').on('xhr.dt', function ( e, settings, json, xhr ) {
            }).DataTable({
            bProcessing: true,
            bServerSide: true,
            responsive: true,
            searching: false,
            ajax:
            {
                url : baseURL+'/pinjaman/pengajuan/list/data',
                dataSrc: 'data',
                data: function(data){
                    data.status_pengajuan = $('#select_status_pengajuan').val();
                    data.start_tgl_pengajuan = $('#start_input_tgl_pengajuan').val();
                    data.end_tgl_pengajuan = $('#end_input_tgl_pengajuan').val();
                    data.anggota = $('#select_anggota').val();
                },
            },
            aoColumns:
            [
                {
                    mData: 'id', visible: false,
                },
                {
                    data: null, orderable: false,
                    className: 'select-checkbox', defaultContent: "",
                },
                {
                    mData: 'DT_RowIndex',
                    className: "dt-body-center", 'name': 'DT_RowIndex',
                },
                {
                    mData: 'no_jkk', sType: "string",
                    className: "dt-body-center", "name": "no_jkk"
                },
                {
                    mData: 'tgl_pengajuan', sType: "string",
                    className: "dt-body-center", "name": "tgl_pengajuan"
                },
                {
                    mData: 'kode_anggota', sType: "string",
                    className: "dt-body-center", "name": "kode_anggota"
                },
                {
                    mData: 'anggota.nama_anggota', sType: "string",
                    className: "dt-body-center", "name": "anggota.nama_anggota",
                    mRender : function(data, type, full)
                    {
                        var markup = '';

                        if (full.anggota)
                        {
                            markup += full.anggota.nama_anggota;
                        }

                        return markup;
                    },
                },
                {
                    mData: 'nama_pinjaman', sType: "string",
                    className: "dt-body-center", "name": "nama_pinjaman"
                },
                {
                    mData: 'besar_pinjam', sType: "string",
                    className: "dt-body-center", "name": "besar_pinjam"
                },
                {
                    mData: 'form_persetujuan', sType: "string",
                    className: "dt-body-center", "name": "form_persetujuan",
                    mRender : function(data, type, full)
                    {
                        var markup = '';

                        if (full.form_persetujuan)
                        {
                            markup += '<a class="btn btn-warning btn-sm" href="'+baseURL+'/'+full.form_persetujuan+'" target="_blank"><i class="fa fa-file"></i></a>';
                        }

                        return markup;
                    },
                },
                {
                    mData: 'status_pengajuan', sType: "string",
                    className: "dt-body-center", "name": "status_pengajuan"
                },
                {
                    mData: 'tgl_acc', sType: "string",
                    className: "dt-body-center", "name": "tgl_acc"
                },
                {
                    mData: 'created_by', sType: "string",
                    className: "dt-body-center", "name": "created_by",
                    mRender : function(data, type, full)
                    {
                        var markup = '';

                        if (full.created_by)
                        {
                            markup += full.created_by.name;
                        }
                        else
                        {
                            markup += '-';
                        }

                        return markup;
                    },
                },
                {
                    mData: 'approved_by', sType: "string",
                    className: "dt-body-center", "name": "approved_by",
                    mRender : function(data, type, full)
                    {
                        var markup = '';

                        if (full.approved_by)
                        {
                            markup += full.approved_by.name;
                        }
                        else
                        {
                            markup += '-';
                        }

                        return markup;
                    },
                },
                {
                    mData: 'keterangan', sType: "string",
                    className: "dt-body-center", "name": "keterangan"
                },
                {
                    mData: 'paid_by_cashier', sType: "string",
                    className: "dt-body-center", "name": "paid_by_cashier",
                    mRender : function(data, type, full)
                    {
                        var markup = '';

                        if (full.paid_by_cashier)
                        {
                            markup += full.paid_by_cashier.name;
                        }
                        else
                        {
                            markup += '-';
                        }

                        return markup;
                    },
                },
                {
                    mData: 'id', sType: "string",
                    className: "dt-body-center", "name": "id",
                    mRender : function(data, type, full)
                    {
                        var markup = '';

                        @if (Auth::user()->isAnggota())
                            if (full.id_status_pengajuan == {{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI }})
                            {
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DIBATALKAN }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Cancel</a>';
                            }
                            else
                            {
                                markup += '-';
                            }
                        @else
                            @can('approve pengajuan pinjaman')
                                if (full.id_status_pengajuan == {{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI }})
                                {
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_SPV }}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Terima</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_KONFIRMASI }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                }
                                else if (full.id_status_pengajuan == {{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_SPV }})
                                {
                                    @can('approve pengajuan pinjaman spv')
                                        markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_SPV }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_ASMAN }}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                        markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_SPV }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                    @endcan
                                }
                                else if (full.id_status_pengajuan == {{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_ASMAN }})
                                {
                                    // temporary skip manager, bendahara, ketua
                                    // markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_ASMAN }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_MANAGER }}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_ASMAN }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN }}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_ASMAN }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                }
                                else if (full.id_status_pengajuan == {{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_MANAGER }})
                                {
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_MANAGER }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_BENDAHARA }}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_MANAGER }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                }
                                else if (full.id_status_pengajuan == {{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_BENDAHARA }})
                                {
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_BENDAHARA }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_KETUA}}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_BENDAHARA }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                }
                                else if (full.id_status_pengajuan == {{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_KETUA }})
                                {
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_KETUA }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN}}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_APPROVAL_KETUA }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                }
                                else if (full.id_status_pengajuan == {{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN }})
                                {
                                    @can('bayar pengajuan pinjaman')
                                        if (full.status_jkk == 1)
                                        {
                                            markup += '<a data-id="'+full.kode_pengajuan+'" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}" class="text-white btn btn-sm mt-1 btn-success btn-konfirmasi">Konfirmasi Pembayaran</a>';
                                        }
                                        else
                                        {
                                            markup += 'JKK Belum di Print';

                                        }
                                    @endcan
                                }

                                markup += '<a data-id="'+full.kode_pengajuan+'" data-code="'+full.kode_jenis_pinjam+'" data-nominal="'+full.besar_ambil+'"  class="text-white btn btn-sm mt-1 btn-info btn-jurnal"><i class="fas fa-eye"></i> Jurnal</a>';
                                markup += '<a class="btn mt-1 btn-sm btn-warning btn-detail" data-id="'+full.kode_pengajuan+'" style="cursor: pointer"><i class="fa fa-info"></i> Info</a>';
                                markup += '<a class="btn mt-1 btn-dark btn-sm btn-lampiran text-white" data-id="'+full.kode_pengajuan+'"><i class="fa fa-file"></i> Lampiran</a>';

                            @endcan
                            @can('bayar pengajuan pinjaman')
                            if (full.id_status_pengajuan == {{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN }}){
                                        if (full.status_jkk == 1)
                                        {
                                            markup += '<a data-id="'+full.kode_pengajuan+'" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN }}" class="text-white btn btn-sm mt-1 btn-success btn-konfirmasi">Konfirmasi Pembayaran</a>';
                                        }
                                        else
                                        {
                                            markup += 'JKK Belum di Print';

                                        }
                                        markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DIBATALKAN }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> BATALKAN</a>';
                                    }
                                    @endcan
                            @can('edit coa after payment')
                              if (full.id_status_pengajuan == {{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}){

                                      markup += '<a data-id="'+full.kode_pengajuan+'" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}" class="text-white btn btn-sm mt-1 btn-danger btn-editcoa1">Edit Coa</a>';
                                    }

                            @endcan

                        @endif

                        return markup;
                    },
                },
            ],
            columnDefs: [
                { "targets": 0,"searchable": false, "orderable": false, 'checkboxes' : { 'selectRow': true } },
                { "targets": 1,"searchable": false, "orderable": false },
                { "targets": 2,"searchable": false, "orderable": false },
                { "targets": 3,"searchable": true, "orderable": true },
                { "targets": 4,"searchable": false, "orderable": true },
                { "targets": 5,"searchable": false, "orderable": false },
                { "targets": 6,"searchable": false, "orderable": false },
                { "targets": 7,"searchable": false, "orderable": false },
                { "targets": 8,"searchable": false, "orderable": false },
                { "targets": 9,"searchable": false, "orderable": false },
                { "targets": 10,"searchable": false, "orderable": false },
                { "targets": 11,"searchable": false, "orderable": false },
                { "targets": 12,"searchable": false, "orderable": true },
            ],
            dom: 'lBrtip',
            buttons: [
                'selectAll',
                'selectNone',
            ],
            select: {
                style: 'multi',
                selector: 'td:first-child'
            },
            fnInitComplete: function (oSettings, json) {

                var _that = this;

                this.each(function (i) {
                    $.fn.dataTableExt.iApiIndex = i;
                    var $this = this;
                    var anControl = $('input', _that.fnSettings().aanFeatures.f);
                    anControl
                        .unbind('keyup search input')
                        .bind('keypress', function (e) {
                            if (e.which == 13) {
                                $.fn.dataTableExt.iApiIndex = i;
                                _that.fnFilter(anControl.val());
                            }
                        });
                    return this;
                });

                return this;
            },
        });

        $('#btnFiterSubmitSearch').click(function(){
			$('#pengajuan-table').DataTable().draw(true);
		});

        $.fn.modal.Constructor.prototype._enforceFocus = function() {};
        $('#start_input_tgl_pengajuan').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd-mm-yyyy'
        });
        $('#end_input_tgl_pengajuan').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd-mm-yyyy'
        });

        $(document).on('click', '.btn-approval', function ()
        {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var old_status = $(this).data('old-status');
            var tgl_transaksi = $('#tgl_transaksi').val();
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
                    html: '<div class="form-group text-left"><label>Keterangan</label><textarea placeholder="Keterangan" name="keterangan" id="keterangan" class="form-control"></textarea></div>',
                    input: 'password',
                    inputAttributes: {
                        name: 'password',
                        placeholder: 'Password',
                        required: 'required',
                        validationMessage:'Password required',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var password = result.value;
                        var formData = new FormData();
                        var token = "{{ csrf_token() }}";
                        var keterangan = $('#keterangan').val();
                        formData.append('_token', token);
                        formData.append('id', id);
                        formData.append('status', status);
                        formData.append('bukti_pembayaran', files[0]);
                        formData.append('password', password);
                        formData.append('id_akun_debet', id_akun_debet);
                        formData.append('old_status', old_status);
                        formData.append('tgl_transaksi', tgl_transaksi);
                        formData.append('keterangan', keterangan);
                        // getting selected checkboxes kode ambil(s)
                        var ids_array = table
                                        .rows({ selected: true })
                                        .data()
                                        .pluck('id')
                                        .toArray();
                        if (ids_array.length != 0)
                        {
                            // append ids array into form
                            formData.append('ids', JSON.stringify(ids_array));
                        }
                        else
                        {
                            formData.append('ids', '['+id+']');
                        }
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
                        })
                    }
                })
            }
        });

        $(document).on('click', '.btn-konfirmasi', function ()
        {
            var id = $(this).data('id');
            var action = $(this).data('action');
            var url = baseURL + '/pinjaman/detail-pembayaran/'+id;

            $.get(url, function( data ) {
                $('#my-modal .form-detail').html(data);
                $('.btn-approval').data('id', id);
                $('#my-modal').modal({
                    backdrop: false
                });
                $('#my-modal').modal('show');
            });

            $('#jenisAkun').trigger( "change" );
        });

        $(document).on('click', '.btn-editcoa', function ()
        {
            var id = $(this).data('id');
            var url = baseURL + '/pinjaman/pengajuan/update/data-coa/'+id;

            var id_akun_debet = $('#code').val();
            var id_jurnal = $('#id_jurnal').val();

            // files is mandatory when status pengajuan pinjaman diterima

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    input: 'password',
                    inputAttributes: {
                        name: 'password',
                        placeholder: 'Password',
                        required: 'required',
                        validationMessage:'Password required',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes',
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        var password = result.value;
                        var formData = new FormData();
                        var token = "{{ csrf_token() }}";
                        // var keterangan = $('#keterangan').val();
                        formData.append('_token', token);
                        formData.append('password', password);
                        formData.append('id_akun_debet', id_akun_debet);
                        formData.append('id_jurnal', id_jurnal);
                        // getting selected checkboxes kode ambil(s)
                        var ids_array = table
                                        .rows({ selected: true })
                                        .data()
                                        .pluck('id')
                                        .toArray();
                        if (ids_array.length != 0)
                        {
                            // append ids array into form
                            formData.append('ids', JSON.stringify(ids_array));
                        }
                        else
                        {
                            formData.append('ids', '['+id+']');
                        }
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
                        })
                    }
                })

        });
        $(document).on('click', '.btn-editcoa1', function ()
        {
            var id = $(this).data('id');
            var action = $(this).data('action');
            var url = baseURL + '/pinjaman/pengajuan/data-coa/'+id;

            $.get(url, function( data ) {
                $('#edit-coa-modal .form-detail').html(data);
                $('.btn-editcoa').data('id', id);
                $('#coa_lama').val(data.akun_kredit);
                $('#id_jurnal').val(data.id);
                $('#edit-coa-modal').modal({
                    backdrop: false
                });
                $('#edit-coa-modal').modal('show');
            });

            $('#jenisAkun2').trigger( "change" );
        });

        $(document).on('click', '.btn-jurnal',function ()
        {
            htmlText = '';
            var id = $(this).data('id');
            $.ajax({
                url: baseURL + '/pinjaman/pengajuan/data-jurnal/' + id,
                success : function (data, status, xhr) {
                    htmlText = data;
                    Swal.fire({
                        title: 'Jurnal Pengajuan',
                        html: htmlText,
                        showCancelButton: false,
                        confirmButtonText: "Tutup",
                        confirmButtonColor: "#00ff00",
                    }).then((result) => {
                        if (result.value) {
                        }
                    });
                },
                error : function (xhr, status, error) {
                    Swal.fire({
                      title: 'Error',
                      html: 'Terjadi Kesalahan',
                      icon: "error",
                      showCancelButton: false,
                      confirmButtonText: "Tutup",
                      confirmButtonColor: "#00ff00",
                    }).then((result) => {
                        if (result.value) {
                        }
                    });
                }
            });
        });

        $(document).on('click', '.btn-detail', function ()
        {
            var id = $(this).data('id');
            var pengajuan = collect(@json($listPengajuanPinjaman)).where('kode_pengajuan', id).first();
            var htmlText = '<div class="container-fluid">' +
                                '<div class="row">' +
                                    '<div class="col-md-6 mx-0 my-2">Created At <br> ' + pengajuan['created_at_view'] + '</div>' +
                                    '<div class="col-md-6 mx-0 my-2">Created By <br> ' + pengajuan['created_by_view'] + '</div>' +
                                    '<div class="col-md-6 mx-0 my-2">Updated At <br> ' + pengajuan['updated_at_view'] + '</div>' +
                                    '<div class="col-md-6 mx-0 my-2">Created By <br> ' + pengajuan['updated_by_view'] + '</div>' +
                                '</div>' +
                            '</div>';

            Swal.fire({
                title: 'Info',
                html: htmlText,
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#00a65a",
            }).then((result) => {
                if (result.value) {
                }
            });
        });

        $(document).on('click', '.btn-lampiran', function ()
        {
            var id = $(this).data('id');
            var pengajuan = collect(@json($listPengajuanPinjaman)).where('kode_pengajuan', id).first();

            var foto_ktp = pengajuan.anggota.foto_ktp;
            var bukti_pembayaran = pengajuan.bukti_pembayaran;
            var slip_gaji = [];

            if(pengajuan.anggota.list_penghasilan != undefined)
            {
                slip_gaji = $.grep(pengajuan.anggota.list_penghasilan, function( n, i ) {
                                return n.id_jenis_penghasilan == 4;
                            });
            }

            var htmlText = '<div class="container-fluid">' +
                                '<div class="row">' +
                                    '<div class="col-md-6 mx-0 my-2 text-left">Form Pengajuan</div>';

                                    if(bukti_pembayaran != '' && bukti_pembayaran != null)
                                    {
                                        htmlText += '<div class="col-md-1 mx-0 my-2 text-left"><a class="mt-1" href="'+baseURL + '/' +bukti_pembayaran+'" download><i class="fa fa-download"></i></a></div>' ;
                                    }
                                    else
                                    {
                                        htmlText += '<div class="col-md-6"></div>';
                                    }

                                    htmlText += '<div class="col-md-6 mx-0 my-2 text-left">KTP</div>' ;

                                    if(foto_ktp != '' && foto_ktp != null)
                                    {
                                        htmlText += '<div class="col-md-1 mx-0 my-2 text-left"><a class= "mt-1" href="'+baseURL + '/' +foto_ktp+'" target="_blank"><i class="fa fa-eye"></i></a></div>' +
                                        '<div class="col-md-1 mx-0 my-2 text-left"><a class= "mt-1" href="'+baseURL + '/' +foto_ktp+'" download><i class="fa fa-download"></i></a></div>';
                                    }
                                    else
                                    {
                                        htmlText += '<div class="col-md-6"></div>';
                                    }

                                    htmlText += '<div class="col-md-6 mx-0 my-2 text-left">Slip Gaji</div>' ;

                                    if(slip_gaji.length != 0)
                                    {
                                        var slip_gaji_url = slip_gaji[0].file_path;
                                        htmlText += '<div class="col-md-1 mx-0 my-2 text-left"><a class="mt-1" href="'+baseURL + '/' + slip_gaji_url+'" download><i class="fa fa-download"></i></a></div>';
                                    }
                                    else
                                    {
                                        htmlText += '<div class="col-md-6"></div>';
                                    }

                    htmlText += '</div>' +
                            '</div>';

            Swal.fire({
                title: 'Lampiran',
                html: htmlText,
                showCancelButton: false,
                confirmButtonText: "Ok",
                confirmButtonColor: "#00a65a",
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
            }else if(jenisAkun == 3)
            {
                // insert new option
                $('#code').append('<option value="133">402.01.000 R/K KOPEGMAR</option>');
            }

            $('#code').trigger( "change" );
        });

        $(document).on('change', '#jenisAkun2', function ()
        {
            // remove all option in code
            $('#code2').empty();

            // get jenis akun
            var jenisAkun = $('#jenisAkun2').val();

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
                    $('#code2').append('<option value="'+bankAccount.id+'"'+ selected +'>'+bankAccount.code+ ' ' + bankAccount.name + '</option>');
                });
            }
            else if(jenisAkun == 1)
            {
                // insert new option
                $('#code2').append('<option value="4" >101.01.102 KAS SIMPAN PINJAM</option>');
            }else if(jenisAkun == 3)
            {
                // insert new option
                $('#code2').append('<option value="133">402.01.000 R/K KOPEGMAR</option>');
            }

            $('#code2').trigger( "change" );
        });

        var $loading = $('#loading').hide();
        $(document)
        .ajaxStart(function () {
            $loading.show();
        })
        .ajaxStop(function () {
            $loading.hide();
        });
        $("#select_anggota").select2({
            ajax: {
                url: '{{ route('anggota-ajax-search') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $(document).on('click', '.btn-download-excel', function ()
        {
            var statusPengajuan = $('#select_status_pengajuan option:selected').val();
            var startTglPengajuan = $('#start_input_tgl_pengajuan').val();
            var endTglPengajuan = $('#end_input_tgl_pengajuan').val();
            var anggota = $('#select_anggota option:selected').val();
            var url = '{{ route("download-pengajuan-pinjaman-excel") }}' + '?status_pengajuan='+statusPengajuan+'&start_tgl_pengajuan='+startTglPengajuan+'&end_tgl_pengajuan='+endTglPengajuan+'&anggota='+anggota;
            window.location.replace(url);
        })

    </script>
@endsection
