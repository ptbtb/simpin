@extends('adminlte::page')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h4>{{ $title }}</h4>
        </div>
        <div class="col-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">User</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)
@section('plugins.Select2', true)

@section('css')
    <style>
        .btn-sm {
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    @can('filter user')
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
                <div class="col-md-4 form-group">
                    <label>Unit</label>
                    <select name="company_id" class="form-control" id="company_id">
                        <option value="">Pilih Semua</option>
                        @foreach ($units as $unit)
                            <option value="{{ $unit->id }}" {{ ($request->company_id && $request->company_id == $unit->id)? 'selected':'' }}>{{ $unit->nama }}</option>
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
    @endcan
    <div class="card">
        <div class="card-header text-right">
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
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">No ANG</th>
                        <th style="width: 20%">NIPP</th>
                        <th>NAMA</th>
                        <th>Jenis Anggota</th>
                        <th >Tanggal Lahir</th>
                        <th>Unit Kerja</th>
                        <th>Status</th>
                        <!-- <th style="width: 15%">Role</th> -->
                        <!-- <th style="width: 10%">No Anggota</th> -->
                        @if (auth()->user()->can('edit anggota') ||
                            auth()->user()->can('delete anggota'))
                            <th style="width: 25%">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
   
    <script>
        $.fn.dataTable.ext.errMode = 'none';
        $('.table').on('xhr.dt', function(e, settings, json, xhr) {}).DataTable({
            bProcessing: true,
            bServerSide: true,
            responsive: true,
            ajax: {
                url: '{{ route('anggota-list-ajax') }}',
                dataSrc: 'data',
                data: function(data) {
                    @if(isset($request->status)) data.status = '{{ $request->status }}'; @endif
                     @if(isset($request->company_id)) data.company_id = '{{ $request->company_id }}'; @endif
                @if(isset($request->id_jenis_anggota)) data.id_jenis_anggota = '{{ $request->id_jenis_anggota }}'; @endif
                    @if (isset($request->filter))
                        data.filter = '{{ $request->filter }}';
                    @endif
                }
            },
            aoColumns: [
                {
                    mData: 'kode_anggota', sType: "string",
                    className: "dt-body-left", "name": "kode_anggota"
                },
                {
                    mData: 'nipp', sType: "string",
                    className: "dt-body-left", "name": "nipp"
                },
                {
                    mData: 'nama_anggota', sType: "string",
                    className: "dt-body-left", "name": "nama_anggota"
                },
                {
                    mData: 'nama_jenis_anggota', sType: "date",
                    className: "dt-body-left", "name": "nama_jenis_anggota"
                },
                {
                    mData: 'tgl_lahir', sType: "string",
                    className: "dt-body-left", "name": "tgl_lahir"
                },
                {
                    mData: 'unit', sType: "string",
                    className: "dt-body-left", "name": "unit"
                },
                {
                    mData: 'status', sType: "string",
                    className: "dt-body-left", "name": "status"
                },
                @if (auth()->user()->can('edit anggota') ||
    auth()->user()->can('delete anggota'))
                    {
                        mData: 'kode_anggota', sType: "string",
                        className: "dt-body-center", "name": "kode_anggota",
                        mRender: function(data, type, full)
                        {
                            var markup = '';
                            var baseURL = {!! json_encode(url('/')) !!};
                            @can('edit user')
                                markup += '<a href="' + baseURL + '/anggota/edit/' + data + '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a> '
                            @endcan
                            @can('delete user')
                                var csrf = '@csrf';
                                var method = '@method("delete")';
                                markup += '<form action="' + baseURL + '/anggota/delete/' + data + '" method="post" style="display: inline"><button  class="btn btn-sm btn-danger" type="submit" value="Delete"><i class="fa fa-trash"></i> Delete</button>@method("delete")@csrf</form>';
                            @endcan
                            return markup;
                        }
                    },
                @endif
                
            ],
            fnInitComplete: function(oSettings, json) {

                var _that = this;

                this.each(function(i) {
                    $.fn.dataTableExt.iApiIndex = i;
                    var $this = this;
                    var anControl = $('input', _that.fnSettings().aanFeatures.f);
                    anControl
                        .unbind('keyup search input')
                        .bind('keypress', function(e) {
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
        $('#company_id').select2();
    </script>
@endsection
