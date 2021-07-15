@extends('adminlte::page')

@section('plugins.Datatables', true)

@section('title')
    {{ $title }}
@endsection

@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="">SHU</a></li>
			<li class="breadcrumb-item active">List SHU</li>
		</ol>
	</div>
</div>
@endsection

@section('css')
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-stripped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Anggota</th>
                                <th>Nama</th>
                                <th>Unit Kerja</th>
                                <th>Tahun SHU</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($shu as $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->kode_anggota }}</td>
                                    <td>{{ $item->anggota->nama_anggota ?? '-' }}</td>
                                    <td>{{ $item->anggota->company->nama ?? '-' }}</td>
                                    <td>{{ $item->year ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('list-shu.downloadCard', [$item->id]) }}" class="btn btn-sm btn-info"><i class="fa fa-download"></i> Download Card</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
    <script>
        $('.table').DataTable();
    </script>
@stop
