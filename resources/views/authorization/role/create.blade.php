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
            <li class="breadcrumb-item"><a href="{{ route('role-list') }}">Role</a></li>
			<li class="breadcrumb-item active">Create</li>
		</ol>
	</div>
</div>
@endsection

@section('css')
    <style>
        .btn-sm{
            font-size: .8rem;
        }

        .form-control{
            font-size: .8rem;
            height: calc(2rem + 2px);
        }
    </style>
@endsection

@section('plugins.Datatables', true)

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('role-create') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Role Name</label>
                            <input type="text" name="name" class="form-control" placeholder="Role Name" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="permission" class="m-0">Permission</label>
                            <div class="row">
                                @foreach ($permissionGroups as $permissionGroup)
                                    <div class="col-md-3 mt-2">
                                        <div class="border p-2 h-100">
                                            <div class="font-weight-bold">{{ $permissionGroup->name }}</div>
                                            <hr class="my-1">
                                            @foreach ($permissionGroup->permissions->where('is_user_permission',0)->sortBy('sequence')->values() as $permission)
                                                <div>
                                                    <input style="cursor: pointer" type="checkbox" name="permissions[]" value="{{ $permission->id }}" id="{{ $permission->id }}"> <label style="cursor: pointer" for="{{ $permission->id }}" class="font-weight-normal">{{ $permission->name }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 offset-md-4 text-center">
                        <button type="submit" class="btn btn-success btn-sm form-control"><i class="fa fa-save"></i> Create Role</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection