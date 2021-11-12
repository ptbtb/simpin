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
    @can('add simpanan')
        <div class="card-header text-right">
            <!-- @if ($request->kode_anggota)
                <a href="{{ route('simpanan-download-pdf', ['from' => $request->from, 'to' => $request->to, 'jenis_simpanan' => $request->jenis_simpanan, 'kode_anggota' => $request->kode_anggota]) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
                <a href="{{ route('simpanan-download-excel', ['from' => $request->from, 'to' => $request->to, 'jenis_simpanan' => $request->jenis_simpanan, 'kode_anggota' => $request->kode_anggota]) }}" class="btn btn-sm btn-warning"><i class="fa fa-download"></i> Download Excel</a>
                <a class="btn btn-success" href="{{ route('simpanan-add', ['kode_anggota' => $request->kode_anggota]) }}"><i class="fas fa-plus"></i>Tambah Transaksi</a>
            @else -->
                <a href="{{ route('simpanan-download-pdf', ['from' => $request->from, 'to' => $request->to, 'jenis_simpanan' => $request->jenis_simpanan]) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
                <a href="{{ route('simpanan-download-excel', ['from' => $request->from, 'to' => $request->to, 'jenis_simpanan' => $request->jenis_simpanan,'jenistrans'=>$request->jenistrans]) }}" class="btn btn-sm btn-warning"><i class="fa fa-download"></i> Download Excel</a>
                <a class="btn btn-success" href="{{ route('simpanan-add') }}"><i class="fas fa-plus"></i> Tambah Transaksi</a>
            <!-- @endif -->
        </div>
    @endcan
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
        <table id="table_anggota" class="table table-striped">
            <thead>
                <tr class="info">
                    <th>No</th>
                    <th style="width: 10%">Kode Simpan</th>
                    <th>Nama Anggota</th>
                    <th>Jenis Simpanan</th>
                    <th>Besar Simpanan</th>
                    <th>User Entry</th>
                    <th>Status</th>
                    <th>Tanggal</th>
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
                        <label>Besar Simpanan</label>
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

@section('js')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    $.fn.dataTable.ext.errMode = 'none';
    var baseURL = {!! json_encode(url('/')) !!};

    $(document).ready(function ()
    {
        initiateSelect2();
        @if($request->jenis_simpanan)
            updateSelect2();
        @endif
        initiateDatatable();
        initiateDatepicker();
        initiateEvent();
    });

    function initiateDatepicker()
    {
        $('#from').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
        $('#to').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'yyyy-mm-dd'
        });
    }

    function initiateDatatable()
    {
        var t = $('.table').DataTable({
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
                        var mark = '<a style="cursor: pointer" class="btn btn-sm btn-info mt-1 text-white" data-action="jurnal" data-id="' + data + '"><i class="fa fa-eye"></i> Jurnal</a>';
                        if(full.id_status_simpanan == {{ STATUS_SIMPANAN_DITERIMA }})
                        {
                            mark = mark + '<a style="cursor: pointer" class="btn btn-sm btn-warning mt-1 text-white" data-action="edit" data-id="' + data + '" data-simpanan="' + full.besar_simpanan + '"><i class="fa fa-edit"></i> Edit</a>';
                        }
                        mark = mark + '@can("edit simpanan")';
                            if(full.id_status_simpanan == {{ STATUS_SIMPANAN_MENUNGGU_APPROVAL }})
                            {
                                mark = mark + '<a style="cursor: pointer" data-id="' + data + '" data-status="{{ STATUS_SIMPANAN_DITERIMA }}" class="text-white btn mt-1 btn-sm btn-success btn-approval"><i class="fas fa-check"></i> Terima</a>';
                                mark = mark + '<a style="cursor: pointer" data-id="' + data + '" data-status="{{ STATUS_SIMPANAN_DITOLAK }}" class="text-white btn mt-1 btn-sm btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                            }
                        mark = mark + '@endcan';
                        return mark;
                    }
                },
            ]
        });

        t.on( 'order.dt search.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
    }

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

    function initiateEvent()
    {
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
                            title: 'Info',
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
                $(".modal-edit-body #besar_simpanan").val( toRupiah(dataBesarSimpanan) );
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
    }

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
