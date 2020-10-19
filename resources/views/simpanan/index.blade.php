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
                <div class="col-md-4 form-group">
                    <label>Jenis Simpanan</label>
                    <select name="jenis_simpanan" id="jenisSimpanan" class="form-control">
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
    @can('add simpanan')
        <div class="card-header text-right">
            <a href="{{ route('simpanan-download-pdf', ['from' => $request->from, 'to' => $request->to, 'jenis_simpanan' => $request->jenis_simpanan]) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
            <a href="{{ route('simpanan-download-excel', ['from' => $request->from, 'to' => $request->to, 'jenis_simpanan' => $request->jenis_simpanan]) }}" class="btn btn-sm btn-warning"><i class="fa fa-download"></i> Download Excel</a>
            <a class="btn btn-success" href="{{ route('simpanan-add') }}"><i class="fas fa-plus"></i> Tambah Transaksi</a>
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
                    <th>Kode Simpan</th>
                    <th>Nama Anggota</th>
                    <th>Jenis Simpanan</th>
                    <th>Besar Simpanan</th>
                    <th>User Entry</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Entri</th>
                    {{-- <th>Action</th> --}}

                </tr>
            </thead>
            <tbody id="fbody">
            </tbody>
        </table>
    </div>
</div><!-- /row -->
@stop

@section('js')
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script>
    $.fn.dataTable.ext.errMode = 'none';
    var baseURL = {!! json_encode(url('/')) !!};

    $(document).ready(function ()
    {
        initiateSelect2();
        updateSelect2();
    });
    $('#from').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd'
    });
    $('#to').datepicker({
        uiLibrary: 'bootstrap4',
        format: 'yyyy-mm-dd'
    });
    var t = $('.table').DataTable({
        processing: true,
        serverside: true,
        responsive: true,
        order: [[ 6, "desc" ]],
        ajax: {
            url: '{{ route('simpanan-list-ajax') }}',
            dataSrc: 'data',
            data: function(data){
                @if(isset($request->from)) data.from = '{{ $request->from }}'; @endif
                @if(isset($request->to)) data.to = '{{ $request->to }}'; @endif
                @if(isset($request->jenis_simpanan)) data.jenis_simpanan = '{{ $request->jenis_simpanan }}'; @endif
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
                mData: 'tanggal_mulai', sType: "string", 
                className: "dt-body-center", "name": "tanggal_mulai"	,
                mRender: function (data, type, full) {
                    if (data == null || data == '') {
                        return '-';
                    }
                    return data;
                }			
            },
            { 
                mData: 'tanggal_entri', sType: "string", 
                className: "dt-body-center", "name": "tanggal_entri"	,
                mRender: function (data, type, full) {
                    if (data == null || data == '') {
                        return '-';
                    }
                    return data;
                }			
            },
            // { 
            //     mData: 'kode_simpan', sType: "string", 
            //     className: "dt-body-center", "name": "action"	,
            //     mRender: function (data, type, full) {
            //         return '-'
            //     }			
            // },
        ]
    });

    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

    function initiateSelect2() {
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
</script>
@stop