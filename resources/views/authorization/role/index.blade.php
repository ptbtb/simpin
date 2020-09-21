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
			<li class="breadcrumb-item active">Role</li>
		</ol>
	</div>
</div>
@endsection

@section('css')
    <style>
        .btn-sm{
            font-size: .8rem;
        }
    </style>
@endsection

@section('plugins.Datatables', true)

@section('content')
    <div class="card">
        <div class="card-header text-right">
            <a href="{{ route('role-create') }}" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Add Role</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 5%">#</th>
                            <th>Role</th>
                            <th>Guard Name</th>
                            <th style="width: 20%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->guard_name }}</td>
                                <td>
                                    <a href="{{ route('role-edit', ['id'=>$role->id]) }}" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i> Edit</a>
                                    <form action="{{ route('role-delete', ['id'=>$role->id]) }}" method="post" style="display: inline">
                                        <button  class="btn btn-sm btn-danger" type="submit" value="Delete">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                        @method('delete')
                                        @csrf
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $('.table').DataTable();
    </script>
@endsection