@extends('adminlte::page')

@section('title', $title)
@section('plugins.Datatables', true)
@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
			<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
			<li class="breadcrumb-item active">Anggota</li>
		</ol>
	</div>
</div>
@stop

@section('css')
    <style>
        .btn-sm{
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <label class="m-0">Filter</label>
    </div>
    <div class="card-body">
        <form action="{{ route('anggota-list') }}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-4 form-group">
                    <label>Status Anggota</label>
                    <select name="status" class="form-control">
                        <option value="">Pilih Semua</option>
                        <option value="aktif" {{ (isset($request->status) && $request->status == 'aktif')? 'selected':'' }}>Aktif</option>
                        <option value="keluar" {{ (isset($request->status) && $request->status == 'keluar')? 'selected':'' }}>Non Aktif</option>
                    </select>
                </div>
                <div class="col-md-4 form-group">
                    <label>Jenis Anggota</label>
                    <select name="id_jenis_anggota" class="form-control">
                        <option value="">Pilih Semua</option>
                        @foreach ($jenisAnggotas as $jenisAnggota)
                            <option value="{{ $jenisAnggota->id_jenis_anggota }}" {{ ($request->id_jenis_anggota && $request->id_jenis_anggota == $jenisAnggota->id_jenis_anggota)? 'selected':'' }}>{{ $jenisAnggota->nama_jenis_anggota }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 form-group" style="margin-top: 26px">
                    <button type="submit" class="btn btn-sm btn-success form-control"><i class="fa fa-filter"></i> Filter</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card">
    @can('add anggota')
        <div class="card-header text-right">
            <a href="{{ route('anggota-download-excel', $request->all()) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download Excel</a>
            <a href="{{ route('anggota-download-pdf', $request->all()) }}" class="btn btn-info btn-sm"><i class="fa fa-download"></i> Download PDF</a>
            @can('import anggota')
                <a href="{{ route('anggota-import-excel') }}" class="btn btn-warning btn-sm"><i class="fa fa-upload"></i> Import Anggota</a>
            @endcan
            @can('add anggota')
                <a href="{{ route('anggota-create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah Anggota</a>
            @endcan
        </div>
    @endcan
    <div class="card-body table-reponseive">
        <table id="table_anggota" class="table table-striped table-condensed">
            <thead>
                <tr class="info">
                    <th><a href="#">No</a></th>
                    <th><a href="#">No Anggota</a></th>
                    <th><a href="#">NIPP</a></th>
                    <th><a href="#">Nama Anggota</a></th>
                    <th><a href="#">Jenis Anggota</a></th>
                    <!--<th ><a href="#">Tempat</a></th>-->
                    <th ><a href="#">Tanggal Lahir</a></th>
                    <th><a href="#">Unit Kerja</a></th>
                    {{-- <th><a href="#">Pekerjaan</a></th> --}}
                    <!--<th><a href="#">Tanggal Masuk</a></th>-->
                    <th><a href="#">Status</a></th>
                    <th><a href="#">Aksi</a></th>
                </tr>
            </thead>
            <tbody id="fbody">
            </tbody>
        </table>
    </div>

</div><!-- /row -->
@stop

@section('css')
@stop

@section('js')
<script>
    $.fn.dataTable.ext.errMode = 'none';
    var table = $('.table').DataTable({
        "processing": true,
        ajax: {
            url: '{{ route('anggota-list-ajax') }}',
            dataSrc: '',
            data: function(data){
                @if(isset($request->status)) data.status = '{{ $request->status }}'; @endif
                @if(isset($request->id_jenis_anggota)) data.id_jenis_anggota = '{{ $request->id_jenis_anggota }}'; @endif
            }
        },
        aoColumns: [
            {
                mData: 'null ', sType: "string",
                className: "dt-body-center", "name": "index",
            },
            {
                mData: 'kode_anggota_prefix', sType: "string",
                className: "dt-body-center", "name": "kodeAnggotaPrefix"
            },
            {
                mData: 'nipp', sType: "string",
                className: "dt-body-center", "name": "nipp"
            },
            {
                mData: 'nama_anggota', sType: "string",
                className: "dt-body-center", "name": "namaAnggota"
            },
            {
                mData: 'jenis_anggota', sType: "string",
                className: "dt-body-center", "name": "jenisAnggota",
                mRender: function(data, type, full)
                {
                    if (data != null)
                    {
                        if (data.nama_jenis_anggota != null && data.nama_jenis_anggota != '')
                        {
                            return data.nama_jenis_anggota;
                        }
                    }
                    return '-';
                }
            },
            {
                mData: 'tgl_lahir_view', sType: "string",
                className: "dt-body-center", "name": "tglLahi",
                mRender: function(data, type, full)
                {
                    if (data != null)
                    {
                        return data;
                    }
                    return '-';
                }
            },
            {
                mData: 'unit_kerja', sType: "string",
                className: "dt-body-center", "name": "unit_kerja"
            },
            {
                mData: 'status', sType: "string",
                className: "dt-body-center", "name": "status"
            },
            {
                mData: 'kode_anggota', sType: "string",
                className: "dt-body-center", "name": "action",
                mRender: function(data, type, full)
                {
                    var markup = '';
                    var baseURL = {!! json_encode(url('/')) !!};
                    @can('edit anggota')
                        markup += '<a href="' + baseURL + '/anggota/edit/' + data + '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a> '
                    @endcan
                    @can('delete anggota')
                        var csrf = '@csrf';
                        var method = '@method("delete")';
                        markup += '<form action="' + baseURL + '/anggota/delete/' + data + '" method="post" style="display: inline"><button  class="btn btn-sm btn-danger" type="submit" value="Delete"><i class="fa fa-trash"></i> Hapus</button>@method("delete")@csrf</form>';
                    @endcan
                    @if(auth()->user()->can('edit anggota') || auth()->user()->can('delete anggota'))
                        if(full['status'] != 'keluar')
                        {
                            markup += '<a href="' + baseURL + '/anggota/keluar-anggota/' + data + '" class="btn btn-sm btn-info"><i class="fa fa-edit"></i> Keluar Anggota</a> '
                        }
                    @endif
                    @if(auth()->user()->can('edit anggota') || auth()->user()->can('delete anggota'))
                        if(full['status'] == 'keluar' && full['sisa_saldo'] > 0)
                        {
                            markup += '<a href="' + baseURL + '/anggota/batal-keluar-anggota/' + data + '" class="btn btn-sm btn-info"><i class="fa fa-edit"></i>Batal Keluar Anggota</a> '
                        }
                    @endif
                    return markup;
                }
            },
        ]
    });

    // add index column
    table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
</script>
@stop
