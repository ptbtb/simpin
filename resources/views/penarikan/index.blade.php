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
@section('plugins.Select2', true)

@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/select/1.3.3/css/select.dataTables.min.css" rel="stylesheet" />
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
                    <select name="status_penarikan" class="form-control input" id="select_status_penarikan">
                        <option value="" selected>All</option>
                        @foreach ($statusPenarikans as $statusPenarikan)
                            <option value="{{ $statusPenarikan->id }}">{{ $statusPenarikan->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Tgl. Penarikan</label>
                    <input type="text" name="tgl_ambil" id="input_tgl_ambil" value="{{ old('tgl_ambil') }}" class="form-control" placeholder="dd-mm-yyyy" autocomplete="off">
                </div>
                <div class="form-group col-md-4">
                    <label>Anggota</label>
                    <select name="anggota" id="select_anggota" class="form-control">
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
            @can('print jkk penarikan')
                <a href="{{ route('penarikan-print-jkk') }}" class="btn btn-sm btn-info"><i class="fas fa-print"></i> Print JKK</a>
                <a href="{{ route('penarikan-list-export-excel', $request->toArray()) }}" class="btn btn-sm btn-success"><i class="fas fa-download"></i> Export Excel</a>
            @endcan
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped" id="penarikan-table">
                <thead>
                    <tr>
                        <th>Kode Ambil</th>
                        <th>#</th>
                        <th>No</th>
                        <th>Tanggal Penarikan</th>
                        <th>Nama Anggota</th>
                        <th>Jenis Simpanan</th>
                        <th>Besar Penarikan</th>
                        <th>Status</th>
                        <th>Tanggal Acc</th>
                        <th>Diajukan Oleh</th>
                        <th>Dikonfirmasi Oleh</th>
                        <th>Keterangan</th>
                        <th>Pembayaran Oleh</th>
                        <th>Bukti Pembayaran</th>
                        <th style="width: 20%">Action</th>
                    </tr>
                </thead>
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
                                <label>Tanggal Pembayaran</label>
                                <input id="tgl_transaksi" type="date" name="tgl_transaksi" class="form-control" placeholder="yyyy-mm-dd" required value="{{ Carbon\Carbon::today()->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jenis Akun</label>
                                <select name="jenis_akun" id="jenisAkun" class="form-control select2" required>
                                    <option value="1">KAS</option>
                                    <option value="2" selected>BANK</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Akun</label>
                                <select name="id_akun_debet" id="code" class="form-control select2" required>
                                    <option value="" selected disabled>Pilih Akun</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Upload Bukti Pembayaran</label>
                                <input type="file" name="bukti_pembayaran" id="buktiPembayaran" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                        <a data-id="" data-status="{{ STATUS_PENGAMBILAN_DITERIMA }}" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN }}"class="text-white btn btn-sm btn-success btn-approval">Bayar</a>
                    
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.datatables.net/select/1.3.3/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script>
        var baseURL = {!! json_encode(url('/')) !!};
        $.fn.dataTable.ext.errMode = 'none';

        var table = $('#penarikan-table').on('xhr.dt', function ( e, settings, json, xhr ) {
            }).DataTable({
            bProcessing: true,
            bServerSide: true,
            responsive: true,
            searching: false,
            ajax:
            {
                url : baseURL+'/penarikan/list/data',
                dataSrc: 'data',
                data: function(data){
                    data.status_penarikan = $('#select_status_penarikan').val();
                    data.tgl_ambil = $('#input_tgl_ambil').val();
                    data.anggota = $('#select_anggota').val();
                },
            },
            aoColumns:
            [
                {
                    mData: 'kode_ambil', visible: false,
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
                    mData: 'tgl_ambil', sType: "string",
                    className: "dt-body-center", "name": "tgl_ambil"
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
                    mData: 'jenis_simpanan', sType: "string",
                    className: "dt-body-center", "name": "jenis_simpanan",
                },
                {
                    mData: 'besar_ambil', sType: "string",
                    className: "dt-body-center", "name": "besar_ambil"
                },
                {
                    mData: 'status_penarikan', sType: "string",
                    className: "dt-body-center", "name": "status_penarikan",
                    mRender : function(data, type, full)
                    {
                        var markup = '';

                        if (full.status_penarikan)
                        {
                            markup += full.status_penarikan.name;
                        }

                        return markup;
                    },
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

                        return markup;
                    },
                },
                {
                    mData: 'description', sType: "string",
                    className: "dt-body-center", "name": "description"
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

                        return markup;
                    },
                },
                {
                    mData: 'bukti_pembayaran', sType: "string",
                    className: "dt-body-center", "name": "bukti_pembayaran",
                    mRender : function(data, type, full)
                    {
                        var markup = '';

                        if (full.bukti_pembayaran)
                        {
                            markup += '<a class="btn btn-warning btn-sm" href="'+baseURL+'/'+full.bukti_pembayaran+'" target="_blank"><i class="fa fa-file"></i></a>';
                        }

                        return markup;
                    },
                },
                {
                    mData: 'kode_ambil', sType: "string",
                    className: "dt-body-center", "name": "kode_ambil",
                    mRender : function(data, type, full)
                    {
                        var markup = '';
                        @can ('delete penarikan')
                        markup += '<a data-id="'+data+'"  class="text-white btn btn-sm btn-danger btn-hapus"<i class="fas fa-remove"></i>Hapus</a>';
                        @endcan

                        @if (Auth::user()->isAnggota())
                            if (full.status_pengambilan == {{ STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI }})
                            {
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI }}" data-status="{{ STATUS_PENGAMBILAN_DIBATALKAN }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Cancel</a>';
                            }
                            else
                            {
                                markup += '-';
                            }
                        @else
                            @can('approve penarikan')
                                if (full.status_pengambilan == {{ STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI }})
                                {
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_SPV }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Terima</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_KONFIRMASI }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                }
                                else if (full.status_pengambilan == {{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_SPV }})
                                {
                                    @can('approve penarikan spv')
                                        markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_SPV }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_ASMAN }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                        markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_SPV }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                    @endcan
                                }
                                else if (full.status_pengambilan == {{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_ASMAN }})
                                {
                                    // temporary skip manager, bendahara, ketua
                                    // markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_ASMAN }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_MANAGER }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_ASMAN }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_ASMAN }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                }
                                else if (full.status_pengambilan == {{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_MANAGER }})
                                {
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_MANAGER }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_BENDAHARA }}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_MANAGER }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                }
                                else if (full.status_pengambilan == {{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_BENDAHARA }})
                                {
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_BENDAHARA }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_KETUA}}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_BENDAHARA }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                }
                                else if (full.status_pengambilan == {{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_KETUA }})
                                {
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_KETUA }}" data-status="{{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN}}" class="text-white btn btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_APPROVAL_KETUA }}" data-status="{{ STATUS_PENGAMBILAN_DITOLAK }}" class="text-white btn btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                }
                                else if (full.status_pengambilan == {{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN }})
                                {
                                    @can('bayar pengajuan pinjaman')
                                        if (full.status_jkk == 1)
                                        {
                                            markup += '<a data-id="'+data+'" data-status="{{ STATUS_PENGAMBILAN_DITERIMA }}" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN }}" class="text-white btn btn-sm btn-success btn-konfirmasi">Konfirmasi Pembayaran</a>';
                                        }
                                        else
                                        {
                                            markup += 'JKK Belum di Print';

                                        }
                                    @endcan
                                }
                                else if (full.status_pengambilan == {{ STATUS_PENGAMBILAN_DITERIMA }})
                                    markup += '<b style="color: green !important"><i class="fas fa-check"></i></b>';
                                else
                                {
                                    markup += '<b style="color: red !important"><i class="fas fa-times"></i></b>';
                                }

                                if (full.status_pengambilan != {{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN }} && full.status_pengambilan != {{ STATUS_PENGAMBILAN_DITERIMA }} && full.status_pengambilan != {{ STATUS_PENGAMBILAN_DITOLAK }} && full.status_pengambilan != {{ STATUS_PENGAMBILAN_DIBATALKAN }})
                                {
                                    markup += '<a data-id="'+data+'" data-code="'+full.code_trans+'" data-nominal="'+full.besar_ambil+'"  class="text-white btn btn-sm btn-info btn-jurnal"><i class="fas fa-eye"></i> Jurnal</a>';
                                }
                            @endcan

                            @can('bayar pengajuan pinjaman')
                            if (full.status_pengambilan == {{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN }})
                                {
                            
                                        if (full.status_jkk == 1)
                                        {
                                            markup += '<a data-id="'+data+'" data-status="{{ STATUS_PENGAMBILAN_DITERIMA }}" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN }}"  class="text-white btn btn-sm btn-success btn-konfirmasi">Konfirmasi Pembayaran</a>';
                                        }
                                        else
                                        {
                                            markup += 'JKK Belum di Print';

                                        }
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
                { "targets": 3,"searchable": false, "orderable": true },
                { "targets": 4,"searchable": true, "orderable": true },
                { "targets": 5,"searchable": false, "orderable": false },
                { "targets": 6,"searchable": false, "orderable": false },
                { "targets": 7,"searchable": false, "orderable": false },
                { "targets": 8,"searchable": true, "orderable": false },
                { "targets": 9,"searchable": true, "orderable": false },
                { "targets": 10,"searchable": true, "orderable": false },
                { "targets": 11,"searchable": false, "orderable": false },
                { "targets": 12,"searchable": false, "orderable": false },
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
			$('#penarikan-table').DataTable().draw(true);
		});

        $.fn.modal.Constructor.prototype._enforceFocus = function() {};
        $('#input_tgl_ambil').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd-mm-yyyy'
        });

        $(document).on('click','.btn-approval', function ()
        {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var old_status = $(this).data('old-status');
            var tgl_transaksi = $('#tgl_transaksi').val();
            var url = '{{ route("penarikan-update-status") }}';

            var files = $('#buktiPembayaran')[0].files;
            var id_akun_debet = $('#code').val();
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
                                    .pluck('kode_ambil')
                                    .toArray();
                    if (ids_array.length != 0)
                    {
                        // append ids array into form
                        formData.append('kode_ambil_ids', JSON.stringify(ids_array));
                    }
                    else
                    {
                        formData.append('kode_ambil_ids', '['+id+']');
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
                });
                }
            });
        });

        $(document).on('click', '.btn-konfirmasi', function ()
        {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var old_status = $(this).data('old-status');
            var action = $(this).data('action');
            var url = baseURL + '/penarikan/detail-transfer/'+id;

            $.get(url, function( data ) {
                $('#my-modal .form-detail').html(data);
                $('.btn-approval').attr('data-id', id);
                $('#my-modal').modal({
                    backdrop: false
                });
                $('#my-modal').modal('show');
            });
            $('#jenisAkun').trigger( "change" );
        });

        $(document).on('click','.btn-hapus', function ()
        {
            var id = $(this).data('id');
            var url = baseURL + '/penarikan/delete';

            
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
                    var keterangan = $('#keterangan').val();
                    formData.append('_token', token);
                    formData.append('id', id);
                    formData.append('keterangan', keterangan);
                    // getting selected checkboxes kode ambil(s)
                    var ids_array = table
                                    .rows({ selected: true })
                                    .data()
                                    .pluck('kode_ambil')
                                    .toArray();
                    if (ids_array.length != 0)
                    {
                        // append ids array into form
                        formData.append('kode_ambil_ids', JSON.stringify(ids_array));
                    }
                    else
                    {
                        formData.append('kode_ambil_ids', '['+id+']');
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
                });
                }
            });
        });

        $(document).on('click', '.btn-jurnal', function ()
        {
            htmlText = '';
            var id = $(this).data('id');
            $.ajax({
                url: baseURL + '/penarikan/data-jurnal/' + id,
                success : function (data, status, xhr) {
                    htmlText = data;
                    Swal.fire({
                        title: 'Jurnal Penarikan',
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
                },
                error : function (xhr, status, error) {
                    Swal.fire({
                        title: 'Error',
                        html: htmlText,
                        icon: "error",
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
    </script>
@endsection
