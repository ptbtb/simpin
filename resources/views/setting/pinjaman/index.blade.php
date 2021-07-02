@extends('adminlte::page')

@section('title', 'Jenis Pinjaman')
@section('plugins.Datatables', true)
@section('content_header')
<h1>Jenis Pinjaman</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <a class="btn btn-success" href="{{ route('jenis-pinjaman-add') }}"><i class="glyphicon glyphicon-subtitles"></i> + Add Jenis Pinjaman</a>
    </div>
    <!-- /.card-header -->
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
                    <th><a href="#">Nama Pinjaman</a></th>
                    <th><a href="#">Lama Angsuran(BLN)</a></th>
                    <th><a href="#">Maks Pinjaman</a></th>
                    <th><a href="#">Jasa</a></th>

                    <th><a href="#">Action</a></th>

                </tr>
            </thead>
            <tbody id="fbody">
                <?php $i = 1; ?>
                @foreach($data['pinjaman'] as $pinjaman)
                <tr>
                    <td >{{$loop->iteration}}</td>
                    <td >{{$pinjaman->kode_jenis_pinjam}}</td>
                    <td >{{$pinjaman->nama_pinjaman}}</td>
                    <td class="text-right">{{$pinjaman->lama_angsuran}}</td>
                    <td class="text-right">{{$pinjaman->maks_pinjam}}</td>
                    <td class="text-right">{{$pinjaman->jasa}}</td>
                    <td >
                        <a class="btn btn-info" href="{{ route('jenis-pinjaman-edit', ['id'=>$pinjaman->kode_jenis_pinjam]) }}"><i class="glyphicon glyphicon-subtitles"></i>edit</a>
                        <a class="btn btn-danger" onclick="return confirm('Yakin Untuk Dihapus?')" href="{{ route('jenis-pinjaman-delete', ['id'=>$pinjaman->kode_jenis_pinjam]) }}"><i class="glyphicon glyphicon-subtitles"></i>hapus</a>
                    </td>

                </tr>
                <?php $i++; ?>
                @endforeach
            </tbody> </table>
    </div>

</div>

@stop

@section('css')
@stop

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
@stop
