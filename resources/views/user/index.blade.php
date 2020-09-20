@extends('adminlte::page')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
    <h4>{{ $title }}</h4>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)

@section('css')
    <style>
        .btn-sm{
            font-size: .8rem;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        @can('add user')
            <div class="card-header text-right">
                <a href="{{ route('user-create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add User</a>
            </div>
        @endcan
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 20%">Email</th>
                        <th>Name</th>
                        <th style="width: 25%">Role</th>
                        <th style="width: 25%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->name }}</td>
                            <td>
                                @if ($user->roles->count())
                                    {{ $user->roles->implode('name',',') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @can('edit user')
                                    <a href="{{ route('user-edit', ['id'=>$user->id]) }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a>
                                @endcan
                                @can('delete user')
                                    <form action="{{ route('user-delete', ['id'=>$user->id]) }}" method="post" style="display: inline">
                                        <button  class="btn btn-sm btn-danger" type="submit" value="Delete">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                        @method('delete')
                                        @csrf
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('.table').DataTable();
    </script>
@endsection