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
    @can('filter user')
        <div class="card">
            <div class="card-header">
                <label class="m-0">Filter</label>
            </div>
            <div class="card-body">
                <form action="{{ route('user-list') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label>Role</label>
                            <select name="role_id" class="form-control">
                                <option value="">Select All</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ ($request->role_id && $request->role_id == $role->id)? 'selected':'' }}>{{ $role->name }}</option>
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
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $.fn.dataTable.ext.errMode = 'none';
        $('.table').DataTable({
            ajax: {
                url: '{{ route('user-list-ajax') }}',
                dataSrc: '',
                data: function(data){
                    @if(isset($request->role_id)) data.role_id = '{{ $request->role_id }}'; @endif
                }
            },
            aoColumns: [
                { 
                    mData: 'number', sType: "string", 
                    className: "dt-body-center", "name": "number",						
                },
                { 
                    mData: 'email', sType: "string", 
                    className: "dt-body-center", "name": "email"						
                },
                { 
                    mData: 'name', sType: "string", 
                    className: "dt-body-center", "name": "name"						
                },
                { 
                    mData: 'roles', sType: "string", 
                    className: "dt-body-center", "name": "roles.name"	,
                    mRender: function(data, type, full) 
                    {
                        return data[0].name;
                    }					
                },
                @if (auth()->user()->can('edit user') || auth()->user()->can('delete user'))
                    { 
                        mData: 'id', sType: "string", 
                        className: "dt-body-center", "name": "id",	
                        mRender: function(data, type, full) 
                        {
                            var markup = ''; 
                            var baseURL = {!! json_encode(url('/')) !!};
                            @can('edit user')
                                markup += '<a href="' + baseURL + '/user/edit/' + data + '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a> '
                            @endcan
                            @can('delete user')
                                var csrf = '@csrf';
                                var method = '@method("delete")';
                                markup += '<form action="' + baseURL + '/user/delete/' + data + '" method="post" style="display: inline"><button  class="btn btn-sm btn-danger" type="submit" value="Delete"><i class="fa fa-trash"></i> Delete</button>@method("delete")@csrf</form>';
                            @endcan
                            return markup;
                        }
                    },
                @endif
            ]
        });
    </script>
@endsection