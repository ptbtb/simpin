@extends('adminlte::page')

@section('title')
    {{ $title }}
@endsection
@section('plugins.Datatables', true)
@section('plugins.Select2', true)
@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="">Simpanan</a></li>
			<li class="breadcrumb-item active">List Simpanan</li>
		</ol>
	</div>
</div>
@endsection
@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <label class="m-0">Filter</label>
    </div>
    <div class="card-body">
        <form action="{{ route('simpanan-list') }}" method="post">
            @csrf
            <input type="hidden" name="status" value="belum lunas">
            <div class="row">
                @if ($request->kode_anggota)
                    <div class="col-md-4 form-group">
                        <label>Kode Anggota</label>
                        <input type="text" name="kode_anggota" value="{{ $request->kode_anggota }}" class="form-control" readonly style="background-color: #f4f6f9">
                    </div>
                @endif
                 <div class="col-md-4 form-group">
                    <label>Jenis Transaksi</label>
                    {!! Form::select('jenistrans', array('S'=>'Semua','A' => 'Saldo Awal', 'T' => 'Transaksi'),$request->jenistrans, ['id' => 'propinsi', 'class' => 'form-control']) !!}
                </div>

                <div class="col-md-4 form-group">
                    <label>Jenis Simpanan</label>
                    <select name="jenis_simpanan" id="jenisSimpanan" class="form-control">
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label>Unit Kerja</label>
                    {!! Form::select('unit_kerja', $unitKerja, $request->unit_kerja, ['class' => 'form-control unitkerja', 'placeholder' => 'Pilih Satu']) !!}
                </div>
                <div class="form-group col-md-4">
                    <label>Anggota</label>
                    <select name="anggota" class="form-control select2" id="select_anggota">
                        <option value="" selected>All</option>
                        {{-- @foreach ($anggotas as $anggota)
                            <option value="{{ $anggota->kode_anggota }}">{{ $anggota->nama_anggota }}</option>
                        @endforeach --}}
                    </select>
                </div>
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
    @can('add simpanan')

            <!-- @if ($request->kode_anggota)
                <a href="{{ route('simpanan-download-pdf', ['from' => $request->from, 'to' => $request->to, 'jenis_simpanan' => $request->jenis_simpanan, 'kode_anggota' => $request->kode_anggota]) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
                <a href="{{ route('simpanan-download-excel', ['from' => $request->from, 'to' => $request->to, 'jenis_simpanan' => $request->jenis_simpanan, 'kode_anggota' => $request->kode_anggota]) }}" class="btn btn-sm btn-warning"><i class="fa fa-download"></i> Download Excel</a>
                <a class="btn btn-success" href="{{ route('simpanan-add', ['kode_anggota' => $request->kode_anggota]) }}"><i class="fas fa-plus"></i>Tambah Transaksi</a>
            @else -->
                <a href="{{ route('simpanan-download-pdf', ['from' => $request->from, 'to' => $request->to, 'jenis_simpanan' => $request->jenis_simpanan]) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
                <a href="{{ route('simpanan-download-excel', ['from' => $request->from, 'to' => $request->to, 'jenis_simpanan' => $request->jenis_simpanan,'jenistrans'=>$request->jenistrans]) }}" class="btn btn-sm btn-warning"><i class="fa fa-download"></i> Download Excel</a>
                <a class="btn btn-success" href="{{ route('simpanan-add') }}"><i class="fas fa-plus"></i> Tambah Transaksi</a>
            <!-- @endif -->

    @endcan
    @can('posting jurnal')
    <a href="{{ route('simpanan-pending-jurnal') }}" class="btn btn-danger btn-sm"><i class="fa fa-check-square"></i>Pending Jurnal</a>
    @endcan
     </div>
    <!-- /.card-header -->
    <div class="card-body">
        <div class="col-6">
            @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
        </div>
        <table id="tableSimpanan" class="table table-striped">
            <thead>
                <tr class="info">
                    <th>No</th>
                    <th style="width: 10%">Kode Simpan</th>
                    <th>Nama Anggota</th>
                    <th>No Anggota</th>
                    <th>Jenis Simpanan</th>
                    <th>Periode</th>
                    <th>Besar Simpanan</th>
                    <th>User Entry</th>
                    <th>Status</th>

                    <th>Posting</th>
                    <th>Input</th>
                    <th style="width: 15%">Action</th>

                </tr>
            </thead>
            <tbody id="fbody">
            </tbody>
        </table>
    </div>
</div><!-- /row -->
@stop

<div id="modal-edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <form action="{{ route('simpanan-edit') }}" method="POST">
        @csrf
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5>Edit Simpanan</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body row modal-edit-body">
                    <input type="hidden" name="kode_simpan" id="kode_simpan" class="form-control" value="">
                    <div class="form-group col-md-12">
                        <label id="labelinput">Besar Simpanan</label>
                        <input type="text" name="besar_simpanan" id="besar_simpanan" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Besar Simpanan">
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Submit</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </form>
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
                                @foreach ($listSumberDana as $sumberDana)
                                    <option value="{{ $sumberDana->id }}">{{ $sumberDana->name }}</option>
                                @endforeach
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
@section('js')
  <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
  <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
  <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script>
var baseURL = {!! json_encode(url('/')) !!};
$.fn.dataTable.ext.errMode = 'none';


        initiateSelect2();
        @if($request->jenis_simpanan)
            updateSelect2();
        @endif
        // initiateDatatable();
        initiateDatepicker();
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


    function initiateDatepicker()
    {
        $('#from').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
        // $('.datepicker').datepicker({
        //     uiLibrary: 'bootstrap4',
        //     format: 'yyyy-mm-dd'
        // });
        $('#to').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
    }


        var table = $('#tableSimpanan').on('xhr.dt', function ( e, settings, json, xhr ) {
            }).DataTable({
            processing: true,
            serverside: true,
            responsive: true,
            // order: [[ 7, "desc" ]],
            ajax: {
                url: '{{ route('simpanan-list-ajax') }}',
                dataSrc: 'data',
                data: function(data){
                    @if(isset($request->from)) data.from = '{{ $request->from }}'; @endif
                    @if(isset($request->to)) data.to = '{{ $request->to }}'; @endif
                    @if(isset($request->jenis_simpanan)) data.jenis_simpanan = '{{ $request->jenis_simpanan }}'; @endif
                    @if(isset($request->kode_anggota)) data.kode_anggota = '{{ $request->kode_anggota }}'; @endif
                    @if(isset($request->anggota)) data.anggota = '{{ $request->anggota }}'; @endif
                    @if(isset($request->jenistrans)) data.jenistrans = '{{ $request->jenistrans }}'; @endif
                }
            },
            aoColumns: [
                {
                    mData: null
                },
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
                    mData: 'kode_anggota', sType: "string",
                    className: "dt-body-center", "name": "kode_anggota" ,
                    mRender: function (data, type, full) {
                        if (data == null || data == '') {
                            return '-';
                        }
                        return data;
                    }
                },
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
                    mData: 'periode_view', sType: "string",
                    className: "dt-body-center", "name": "periode_view" ,
                    mRender: function (data, type, full) {
                      var mark='';

                      mark = mark + '@can("edit simpanan")';
                          mark = mark + '<a style="cursor: pointer" class="btn btn-sm btn-warning mt-1 mr-1 text-white" data-action="editperiode" data-id="' + full.kode_simpan + '" data-periode="' + full.periode_full_view + '"><i class="fa fa-edit"></i> Edit</a>';

                      mark = mark + '@endcan';
                        if (data == null || data == '') {
                            return '-'+'<br>'+mark;
                        }
                        return data+'<br>'+mark;
                    }
                },
                {
                    mData: 'besar_simpanan_rupiah', sType: "string",
                    className: "dt-body-center", "name": "besar_simpanan_rupiah"	,
                    mRender: function (data, type, full) {
                      var mark='';
                      if(full.id_status_simpanan == {{ STATUS_SIMPANAN_DITERIMA }})
                      {
                          mark = mark + '<a style="cursor: pointer" class="btn btn-sm btn-warning mt-1 mr-1 text-white" data-action="edit" data-id="' + full.kode_simpan + '" data-simpanan="' + full.besar_simpanan + '" data-periode="' + full.periode_view + '"><i class="fa fa-edit"></i> Edit</a>';
                      }
                      mark = mark + '@can("edit simpanan")';
                          if(full.id_status_simpanan == {{ STATUS_SIMPANAN_MENUNGGU_APPROVAL }})
                          {
                              mark = mark + 'Menjadi : '+full.temp_besar_simpanan_rupiah;
                              mark = mark + '<a style="cursor: pointer" data-id="' + full.kode_simpan + '" data-status="{{ STATUS_SIMPANAN_DITERIMA }}" class="text-white btn mt-1 btn-sm mr-1 btn-success btn-approval"><i class="fas fa-check"></i> Terima</a>';
                              mark = mark + '<a style="cursor: pointer" data-id="' + full.kode_simpan + '" data-status="{{ STATUS_SIMPANAN_DITOLAK }}" class="text-white btn mt-1 btn-sm mr-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                          }

                      mark = mark + '@endcan';

                        if (data == null || data == '') {
                            return '-' +'<br>'+mark;
                        }

                        return data+'<br>'+mark;
                    }
                },
                {
                    mData: 'u_entry', sType: "string",
                    className: "dt-body-center", "name": "u_entry"	,
                    mRender: function (data, type, full) {
                        if (data == null || data == '') {
                            return '-';
                        }
                        return data;
                    }
                },
                {
                    mData: 'status_simpanan_view', sType: "string",
                    className: "dt-body-center", "name": "status_simpanan_view"	,
                    mRender: function (data, type, full) {
                        if (data == null || data == '') {
                            return '-';
                        }
                        return data;
                    }
                },

                {
                    mData: 'tgl_transaksi', sType: "string",
                    className: "dt-body-center", "name": "tgl_transaksi" ,
                    mRender: function (data, type, full) {
                        if (data == null || data == '') {
                            return '-';
                        }
                        return data;
                    }
                },
                {
                    mData: 'tanggal_entri', sType: "string",
                    className: "dt-body-center", "name": "tanggal_entri" ,
                    mRender: function (data, type, full) {
                        if (data == null || data == '') {
                            return '-';
                        }
                        return data;
                    }
                },
                {
                    mData: 'kode_simpan', sType: "string",
                    className: "dt-body-center", "name": "action"	,
                    mRender: function (data, type, full) {
                        var mark = '<a style="cursor: pointer" class="btn btn-sm btn-info mt-1 mr-1 text-white" data-action="jurnal" data-id="' + data + '"><i class="fa fa-eye"></i> Jurnal</a>';

                        @can('edit coa after payment')
                          if (full.id_status_simpanan == {{ STATUS_SIMPANAN_DITERIMA }}){

                                  mark += '<a data-id="'+full.kode_simpan+'"  class="text-white btn btn-sm mt-1 mr-1 btn-danger btn-editcoa1">Edit Coa</a>';
                                }

                        @endcan
                        mark = mark + '@can("delete simpanan")';
                                mark = mark + '<a style="cursor: pointer" data-id="' + data + '"  class="text-white btn mt-1 mr-1 btn-sm btn-danger btn-hapus"><i class="fas fa-remove"></i> Hapus</a>';
                        mark = mark + '@endcan';
                        return mark;
                    }
                },
            ]
        });

        table.on( 'order.dt search.dt', function () {
            table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();


    function initiateSelect2() {
        $(".unitKerja").select2();
        $("#jenisSimpanan").select2({
            placeholder: 'Pilih Semua',
            allowClear: true,
            ajax: {
                url: '{{ route('jenis-simpanan-search') }}',
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
    }

    function updateSelect2()
    {
        // Fetch the preselected item, and add to the control
        var challengeSelect = $('#jenisSimpanan');
        $.ajax({
            type: 'GET',
            url: '{{ route('jenis-simpanan-search') }}' + '/' +'{{ $request->jenis_simpanan }}'
        }).then(function (data) {
            // create the option and append to Select2
            var option = new Option(data.view_nama, data.kode_jenis_simpan, true, true);
            challengeSelect.append(option).trigger('change');

            // manually trigger the `select2:select` event
            challengeSelect.trigger({
                type: 'select2:select',
                params: {
                    data: data
                }
            });
        });
    }


        // event button table on click
        $(document).on('click', 'a', function ()
        {
            var action = $(this).data('action');

            // action info
            if (action == 'info')
            {
                var startDate = ($(this).data('start-date') == null)? '-':$(this).data('start-date');
                var entryDate = ($(this).data('entry-date') == null)? '-':$(this).data('entry-date');
                var uEntry = ($(this).data('u-entry') == null)? '-':$(this).data('u-entry');

                var htmlText = '<div class="container-fluid" style="font-size : 14px">' +
                                    '<div class="row">' +

                                        '<div class="col-md-6 mx-0 my-2">Tangggal Transaksi <br> <b>' + entryDate + '</b></div>' +
                                        '<div class="col-md-6 mx-0 my-2"></div>' +
                                        '<div class="col-md-6 mx-0 my-2">User Entri <br> <b>' + uEntry + '</b></div>' +
                                    '</div>' +
                                '</div>';

                Swal.fire({
                    title: 'Info',
                    html: htmlText,
                    showCancelButton: false,
                    confirmButtonText: "Tutup",
                    confirmButtonColor: "#00ff00",
                });
            }
            else if(action == 'jurnal')
            {
                var dataId = $(this).data('id');
                $.ajax({
                    url: baseURL + '/simpanan/jurnal/' + dataId,
                    success: function (data, status, xhr)
                    {
                        var htmlText = data;
                        Swal.fire({
                            title: 'Jurnal Simpanan',
                            html: htmlText,
                            showCancelButton: false,
                            confirmButtonText: "Tutup",
                            confirmButtonColor: "#00ff00",
                        });
                    },
                    error: function (xhr,status,error)
                    {
                        Swal.fire({
                            title: 'Error',
                            html: 'Terjadi Kesalahan',
                            icon: "error",
                            showCancelButton: false,
                            confirmButtonText: "Tutup",
                            confirmButtonColor: "#00ff00",
                        });
                    }
                });
            }
            else if(action == 'edit')
            {
                var dataBesarSimpanan = $(this).data('simpanan');
                var dataId = $(this).data('id');

                $('#modal-edit').modal({
                    backdrop: false
                });
                $('#modal-edit').modal('show');

                $(".modal-edit-body #kode_simpan").val( dataId );
                $(".modal-edit-body #besar_simpanan").remove();
                $(".modal-edit-body").append('<input type="text" name="besar_simpanan" value="'+toRupiah(dataBesarSimpanan)+'" id="besar_simpanan" onkeypress="return isNumberKey(event)" class="form-control" placeholder="Besar Simpanan">');
                $(".modal-edit-body #labelinput").html('Besar Simpanan');;
                // $(".modal-edit-body #besar_simpanan").val( toRupiah(dataBesarSimpanan) );
            }
            else if(action == 'editperiode')
            {
                var dataperiode = $(this).data('periode');
                var dataId = $(this).data('id');

                $('#modal-edit').modal({
                    backdrop: false
                });
                $('#modal-edit').modal('show');

                $(".modal-edit-body #kode_simpan").val( dataId );
                $(".modal-edit-body #besar_simpanan").remove();;
                $(".modal-edit-body").append('<input type="text" name="periode" value="'+dataperiode+'" id="besar_simpanan"  class="form-control datepicker" placeholder="Periode">');
                $(".modal-edit-body #labelinput").html('Periode');;
                $('.datepicker').datepicker({
                    uiLibrary: 'bootstrap4',
                    format: 'yyyy-mm-dd'
                });
            }
        });

        $(document).on('keyup', '#besar_simpanan', function ()
        {
            var besarSimpanan = $(this).val().toString();
            besarSimpanan = besarSimpanan.replace(/[^\d]/g, "",'');
            $('#besar_simpanan').val(toRupiah(besarSimpanan));
            /*if(tipeSimpanan === '502.01.000'){
                if(besarSimpanan > besarSimpananSukarela) {
                    errMessage('warningText', 'Jumlah besar simpanan melebihi 65% dari total gaji/bulan');
                } else {
                    clearErrMessage('warningText');
                }
            }*/
            /*if(tipeSimpanan === '411.01.000'){
                if(besarSimpanan > besarSimpananPokok) {
                    errMessage('warningText', 'Jumlah besar simpanan melebihi sisa angsuran');

                } else {
                    clearErrMessage('warningText');
                }
            }*/
        });

        $(document).on('click', '.btn-approval', function ()
        {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var url = '{{ route("simpanan-update-status") }}';

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
                    formData.append('_token', token);
                    formData.append('id', id);
                    formData.append('status', status);
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
                    })
                }
            })

        });

        $(document).on('click', '.btn-hapus', function ()
        {
            var id = $(this).data('id');
            var url = '{{ route("simpanan-delete") }}';

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
                    formData.append('_token', token);
                    formData.append('id', id);
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
                    })
                }
            })

        });


        $(document).on('click', '.btn-jurnal',function ()
        {
            htmlText = '';
            var id = $(this).data('id');
            $.ajax({
                url: baseURL + '/simpanan/data-jurnal/' + id,
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
        var bankAccountArray = [];

        // get bank account number from php
        @foreach($bankAccounts as $key => $bankAccount)
            bankAccountArray[{{ $loop->index }}]={ id : {{ $bankAccount->id }}, code: '{{ $bankAccount->CODE }}', name: '{{ $bankAccount->NAMA_TRANSAKSI }}' };
        @endforeach

        // trigger to get kas or bank select option
        var listSumberDana = collect(@json($listSumberDana));
        $(document).on('change', '#jenisAkun', function ()
        {
            // remove all option in code
            $('#code').empty();

            // get jenis akun
            var jenisAkun = $('#jenisAkun').val();
            selectedSumberDana = listSumberDana.where('id', parseInt(jenisAkun)).first();
            currentCodes = collect(selectedSumberDana.codes);
            var pattern = "";
            currentCodes.each(function (code)
            {
                if(code.id == 22)
                {
                    pattern = pattern + '<option value="'+ code.id +'" selected>'+ code.CODE +' '+ code.NAMA_TRANSAKSI +'</option>';
                }
                else
                {
                    pattern = pattern + '<option value="'+ code.id +'">'+ code.CODE +' '+ code.NAMA_TRANSAKSI +'</option>';
                }
            });
            $('#code').html(pattern);
            /* if(jenisAkun == 2)
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
                $('#code').append('<option value="174" >409.01.000 SIMPANAN KHUSUS</option><option value="182" >409.03.000 SIMPANAN KHUSUS PAGU</option><option value="133" >402.01.000 R/K KOPEGMAR</option>');

            } */

            $('#code').trigger( "change" );
        });

    $(document).on('click', '.btn-editcoa', function ()
    {
        var id = $(this).data('id');
        var url = baseURL + '/simpanan/update/data-coa/'+id;

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
    $('#edit-coa-modal').on('shown.bs.modal', function() {
    $(document).off('focusin.modal');
});
    $(document).on('click', '.btn-editcoa1', function ()
    {
        var id = $(this).data('id');
        var action = $(this).data('action');
        var url = baseURL + '/simpanan/data-coa/'+id;

        $.get(url, function( data ) {
            $('#edit-coa-modal .form-detail').html(data);
            $('.btn-editcoa').data('id', id);
            $('#coa_lama').val(data.akun_debet);
            $('#id_jurnal').val(data.id);
            $('#edit-coa-modal').modal({
                backdrop: false
            });
            $('#edit-coa-modal').modal('show');
        });

        $('#jenisAkun').trigger( "change" );
    });

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

    function isNumberKey(evt)
    {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

        return true;
    }
</script>
@stop
