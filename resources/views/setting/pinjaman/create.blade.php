@extends('adminlte::page')

@section('title', 'Add Pinjaman')
@section('plugins.Datatables', true)
@section('content_header')
<h1>Add Pinjaman</h1>
@stop

@section('content')
<div class="card">
    <div class="card-header">

    </div>
    <!-- /.card-header -->
    <div class="card-body"><div class="row mt">
            <div class="col-lg-12">
                <div class="form-panel" style="width:50%;">
                    <form action="/setting/pinjaman/store" method="post" id="form">
                        {{csrf_field()}}
                        <div class="form-group">
                            <label>Kode Simpanan</label>
                            <input type="text" class="form-control" name="kode_jenis_pinjam" size="54" value="" />
                        </div>
                        <div class="form-group">
                            <label>Jenis Simpanan</label>
                            <input type="text" class="form-control" name="nama_pinjaman" size="54" value=""/>
                        </div>
                        <div class="form-group">
                            <label>Lama Angsuran </label>
                            <input type="text" class="form-control" name="lama_angsuran" >
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
                            <label>Maksimal Pinman</label>
                            <input type="text" class="form-control" onkeypress="return isNumberKey(event)" name="maks_pinjam" size="54" value=""/>
                        </div>
                        <div class="form-group">
                            <label>Jasa(%)</label>
                            <input type="text" class="form-control" name="bunga" >
                        </div>
                        
                        <div class="form-group">
                            <label>User Entri</label>
                            <input type="text" class="form-control" name="u_entry" size="54" value="{{ Auth::user()->name }}" readonly="">
                        </div>
                        <div class="form-group">
                            <label>Tanggal Entri</label>
                            <input type="date" class="form-control" name="tgl_entri" size="54" value="<?php echo date("Y-m-d"); ?>" readonly=""/>
                        </div>
                        <button class="btn btn-info"><span class='glyphicon glyphicon-pencil'></span> Submit</button>
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