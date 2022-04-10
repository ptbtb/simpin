@extends('adminlte::page')

@section('plugins.Datatables', true)

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
                <li class="breadcrumb-item active">Sumber Dana</li>
            </ol>
        </div>
    </div>
@endsection

@section('css')
@endsection

@section('content')
    <div class="card">
        <div class="card-header text-right">
            @can('add sumber dana')
                <a href="{{ route('sumber-dana.create') }}" class="btn btn-xs btn-success"><i class="fa fa-plus"></i> Tambah
                    Sumber Dana
                </a>
            @endcan
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($listSumberDana as $sumberDana)
                        <tr>
                            <td>
                                {{ $loop->iteration }}
                            </td>
                            <td>
                                {{ $sumberDana->name }}
                            </td>
                            <td>
                                {{ $sumberDana->created_at->toDateString() }}
                            </td>
                            <td class="d-flex">
                                @can('edit sumber dana')
                                    <a href="{{ route('sumber-dana.edit', [$sumberDana->id]) }}"
                                        class="btn btn-xs btn-warning"><i class="fa fa-edit"></i> Edit
                                    </a>
                                @endcan
                                @can('delete sumber dana')
                                    <form action="{{ route('sumber-dana.destroy', [$sumberDana->id]) }}" method="post">
                                        @method('delete')
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-xs ml-1">
                                            <i class="fa fa-trash"></i>
                                            Delete
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@stop

@section('js')
    <script>
        $('.table').DataTable();
    </script>
@stop
