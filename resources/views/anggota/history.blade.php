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
            <li class="breadcrumb-item"><a href="{{ route('anggota-list') }}">Anggota</a></li>
			<li class="breadcrumb-item active">{{ $title }}</li>
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
    <div class="card-header">
         
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th >Action</th>
                    <th>Nilai lama</th>
                    <th>Nilai Baru</th>
                    <th>Oleh</th>
                    <th>Tgl</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($list as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->event }}</td>
                    
                    <td>@foreach ($item->old_values as $key=>$old)
                        <b>{{ $key }}</b>: {{ $old }}<br />
                    @endforeach</td>
                    <td>@foreach ($item->new_values as  $keys=>$new)
                        <b>{{ $keys }}</b>: {{ $new }}<br />
                    @endforeach</td>
                    <td>{{ ($item->user_id!==null)?$item->user->name:'' }}</td>
                    <td>{{ $item->created_at }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
<script>
    $('.table').DataTable();
    $('.datepicker').datepicker({
        format: "dd-mm-yyyy"
    });

    $('input.datepicker').bind('keyup keydown keypress', function (evt) {
        return true;
    });
</script>
@endsection