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
                <li class="breadcrumb-item active">Jkk Printed</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)

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
                <form action="{{ route('jkk-printed-list') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>JKK Type</label>
                            <select name="type_id" class="form-control">
                                <option value="">Select All</option>
                                @foreach ($types as $type)
                                    <option value="{{ $type->id }}"
                                        {{ $request->type_id && $request->role_id == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1 form-group" style="margin-top: 26px">
                            <button type="submit" class="btn btn-sm btn-success form-control">
                                <i class="fa fa-filter"></i>Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcan
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 20%">No Jkk</th>
                        <th>Type</th>
                        <th>Created Date</th>
                        <th>Created By</th>
                        <th style="width: 25%">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $.fn.dataTable.ext.errMode = 'none';
        $('.table').DataTable({
            processing: true,
            serverside: true,
            ajax: {
                url: '{{ route('jkk-printed-data') }}',
                dataSrc: 'data',
                data: function(data) {
                    @if (isset($request->type_id)) data.type_id = '{{ $request->type_id }}'; @endif
                }
            },
            aoColumns: [{
                    mData: 'id',
                    sType: "string",
                    className: "dt-body-center",
                    "name": "id"
                },
                {
                    mData: 'jkk_number',
                    sType: "string",
                    className: "dt-body-center",
                    "name": "jkk_number"
                },
                {
                    mData: 'jkk_printed_type.name',
                    sType: "string",
                    className: "dt-body-center",
                    "name": "jkk_printed_type.name"
                },
                {
                    mData: 'printed_at_view',
                    sType: "string",
                    className: "dt-body-center",
                    "name": "printed_at_view"
                },
                {
                    mData: 'printed_by_view',
                    sType: "string",
                    className: "dt-body-center",
                    "name": "printed_by_view"
                },
                {
                    mData: 'id',
                    sType: "string",
                    className: "text-center",
                    mRender: function(data, type, full) {
                        var link = '<a href="{{ route("jkk-printed-reprint", [""]) }}/'+data+'" class="btn btn-sm btn-info" target="_blank">' +
                                        '<i class="fa fa-print"></i> Reprint' +
                                    '</a>';

                        return link;
                    }
                },
            ]
        });
    </script>
@endsection
