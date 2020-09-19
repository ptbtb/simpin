@extends('adminlte::page')

@section('title', $data['judul'])
@section('plugins.Datatables', true)
@section('content_header')
<h1>{{$data['judul']}}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <a class="btn btn-primary" href="/anggota"><i class="glyphicon glyphicon-subtitles"></i> Aktif</a>
        <a class="btn btn-primary" href="/anggota/all"><i class="glyphicon glyphicon-subtitles"></i> Semua</a>

    </div>
    <!-- /.card-header -->
    <div class="card-body">

        <table id="table_anggota" class="table table-bordered table-striped table-condensed">
            <thead>
                <tr class="info">
                    <th><a href="#">No</a></th>
                    <th><a href="#">No Anggota</a></th>
                    <th><a href="#">NIPP</a></th>
                    <th><a href="#">Nama Anggota</a></th>
                    <!--<th ><a href="#">Tempat</a></th>-->
                    <th ><a href="#">Tanggal Lahir</a></th>
                    <th><a href="#">Pekerjaan</a></th>
                    <!--<th><a href="#">Tanggal Masuk</a></th>-->
                    <th><a href="#">Status</a></th>
                    <th><a href="#">Aksi</a></th>

                </tr>
            </thead>
            <tbody id="fbody">
                <?php $i = 1; ?>
                @foreach($data['anggota'] as $anggota)
                <tr>
                    <td >{{$loop->iteration}}</td>
                    <td >{{$anggota->kode_anggota}}</td>
                    <td >{{$anggota->nipp}}</td>
                    <td >{{$anggota->nama_anggota}}</td>
                    <!--<td >{{$anggota->tempat_lahir}}</td>-->
                    <td >{{$anggota->tgl_lahir}}</td>
                    <td >{{$anggota->lokasi_kerja}}</td>
                    <!--<td >{{$anggota->tgl_masuk}}</td>-->
                    <td >{{$anggota->status}}</td>
                    <td >
                        <h6><a class="badge badge-info" href="/anggota/edit/{{$anggota->kode_anggota}}"><i class="glyphicon glyphicon-subtitles"></i>edit</a></h6>
                        <h6><a class="badge badge-danger" onclick="return confirm('Yakin Untuk Dihapus?')" href="/anggota/destroy/{{$anggota->kode_anggota}}"><i class="glyphicon glyphicon-subtitles"></i>Hapus</a></h6>
                    </td>

                </tr>   
                <?php $i++; ?>
                @endforeach
            </tbody> </table>
    </div>

</div><!-- /row -->
@stop

@section('css')
@stop

@section('js')
<script>
    $(document).ready(function () {
        $("#table_anggota").DataTable({
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