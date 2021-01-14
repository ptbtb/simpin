@extends('adminlte::page')

@section('title') 
    {{ $title }}
@endsection

@section('plugins.Datatables', true)

@section('content_header')
    <h1>{{ $title }}</h1>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <a class="btn btn-success" href="{{ route('status-pengajuan-add') }}"><i class="glyphicon glyphicon-subtitles"></i> + Add Status Pengajuan</a>
    </div>
    <div class="card-body">
        <div class="col-6">
            @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            @endif
        </div>
        <table id="table_work" class="table table-bordered table-striped table-condensed">
            <thead>
                <tr class="info">
                    <th><a href="#">No</a></th>
                    <th><a href="#">Kode</a></th>
                    <th><a href="#">Status Pengajuan</a></th>
                    <th><a href="#">Batas Pengajuan</a></th>
                    <th><a href="#">Action</a></th>

                </tr>
            </thead>
            <tbody id="fbody">
                <?php $i = 1; ?>
                @foreach($statusPengajuans as $statusPengajuan)
                <tr>
                    <td >{{$loop->iteration}}</td>
                    <td >{{$statusPengajuan->id}}</td>
                    <td >{{$statusPengajuan->name}}</td>
                    <td class="text-right">
                        Rp. {{ number_format($statusPengajuan->batas_pengajuan,0,",",".") }}
                    </td>
                    <td >
                        <a class="btn btn-info" href="{{ route('status-pengajuan-edit', ['id'=>$statusPengajuan->id]) }}"><i class="glyphicon glyphicon-subtitles"></i>edit</a>
                        <a class="btn btn-danger" onclick="return confirm('Yakin Untuk Dihapus?')" href="{{ route('status-pengajuan-delete', ['id'=>$statusPengajuan->id]) }}"><i class="glyphicon glyphicon-subtitles"></i>hapus</a>
                    </td>
                </tr>   
                <?php $i++; ?>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('css')
@endsection

@section('js')
<script>
    $(document).ready(function () {
        $("#table_work").DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
        });
    });
</script>
@endsection