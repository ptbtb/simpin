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
			<li class="breadcrumb-item active">User</li>
		</ol>
	</div>
</div>
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
                        @if (auth()->user()->can('edit user') || auth()->user()->can('delete user'))
                            <th style="width: 25%">Action</th>
                        @endif
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
                            @if (auth()->user()->can('edit user') || auth()->user()->can('delete user'))
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
                            @endif
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