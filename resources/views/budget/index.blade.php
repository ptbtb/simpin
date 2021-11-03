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
                <li class="breadcrumb-item active">Budget</li>
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
    <div class="card">
        <div class="card-header text-right">
            @can('export budget')
                <a href="{{ route('budget.excel') }}" class="btn btn-sm btn-info"><i class="fa fa-download"></i> Export excel</a>
            @endcan
            @can('add budget')
                <a href="{{ route('budget.create') }}" class="btn btn-sm btn-success"><i class="fa fa-plus"></i> Add Budget</a>
            @endcan
            @can('import budget')
                <a href="{{ route('budget.import') }}" class="btn btn-sm btn-primary"><i class="fa fa-upload"></i> Import Budget</a>
            @endcan
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Name</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Created By</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
                url: '{{ route('budget.data') }}',
                dataSrc: 'data'
            },
            aoColumns: [
                {
                    mData: 'id',
                    sType: "string",
                    className: "dt-body-center",
                    name: "id"
                },
                {
                    mData: 'name',
                    sType: "string",
                    className: "dt-body-center",
                    name: "name"
                },
                {
                    mData: 'date_view',
                    sType: "string",
                    className: "dt-body-center",
                    name: "date_view"
                },
                {
                    mData: 'amount',
                    sType: "string",
                    className: "dt-body-center",
                    name: "amount"
                },
                {
                    mData: 'created_by_view',
                    sType: "string",
                    className: "dt-body-center",
                    name: "created_by_view"
                },
                {
                    mData: 'id',
                    sType: "string",
                    className: "dt-body-center",
                    name: "action",
                    mRender: function (data, type, full)
                    {
                        var res = '';
                        @can('edit budget')
                            var url = "{{ route('budget.edit', [0]) }}";
                            url = url.replace(0, data);
                            res = res + '<a href="'+url+'" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a>';
                        @endcan
                        return res;
                    }
                },
            ]
        });
    </script>
@endsection
