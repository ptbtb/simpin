@extends('adminlte::page')

@section('title', 'Edit Simpanan')
@section('plugins.Datatables', true)
@section('content_header')
<h1>Edit Simpanan</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        
    </div>
    <!-- /.card-header -->
    <div class="card-body"><div class="row mt">
            <div class="col-lg-12">
                <div class="form-panel" style="width:50%;">
                    <form action="/setting/simpanan/update" method="post" id="form">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label>Kode Simpanan</label>
                            <input type="text" class="form-control" name="kode_jenis_simpan" size="54" value="{{$simpanan->kode_jenis_simpan}}" readonly=""/>
                        </div>
                        <div class="form-group">
                            <label>Jenis Simpanan</label>
                            <input type="text" class="form-control" name="nama_simpanan" size="54" value="{{$simpanan->nama_simpanan}}"/>
                        </div>
                        <script>
                            function isNumberKey(evt)
                            {
                                var charCode = (evt.which) ? evt.which : event.keyCode
                                if (charCode > 31 && (charCode < 48 || charCode > 57))
                                    return false;
                                return true;
                            }
                        </script>
                        <div class="form-group">
                            <label>Besar Simpanan</label>
                            <input type="text" class="form-control" onkeypress="return isNumberKey(event)" name="besar_simpanan" size="54" value="{{$simpanan->besar_simpanan}}"/>
                        </div>
                        <div class="form-group">
                            <label>Tanggal Tagih</label>
                            <input type="text" class="form-control" name="tgl_tagih" value="{{$simpanan->tgl_tagih}}">
                        </div>
                        <div class="form-group">
                            <label>Hari Jatuh Tempo </label>
                            <input type="text" class="form-control" name="hari_jatuh_tempo" value="{{$simpanan->hari_jatuh_tempo}}">
                        </div>
                        <div class="form-group">
                            <label>User Entri</label>
                            <input type="text" class="form-control" name="u_entry" size="54" value="{{ Auth::user()->name }}" readonly="">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Entri</label>
                            <input type="date" class="form-control" name="tgl_entri" size="54" value="{{$simpanan->tgl_entri}}" readonly=""/>
                        </div>
                        <button class="btn btn-info"><span class='glyphicon glyphicon-pencil'></span> Update</button>
                    </form>
                </div>
            </div></div>
    </div>

</div><!-- /row -->
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