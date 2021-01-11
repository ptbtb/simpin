@extends('adminlte::page')
@section('title') 
    {{ $title }})
@endsection

@section('plugins.Datatables', true)

@section('content_header')  
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
			<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
			<li class="breadcrumb-item"><a href="{{ route('jenis-simpanan-list') }}">Jenis Simpanan</a></li>
			<li class="breadcrumb-item active">Create</li>
		</ol>
	</div>
</div>
@endsection

@section('content')
    <form action="{{ route('jenis-simpanan-add') }}" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                           <div class="col-md-12 mb-2">
                               <label>Tanggal Entri : <?php echo date("d M Y"); ?></label>
                           </div>
                            <div class="col-md-6 form-group">
                                <label>Kode Jenis Simpan</label>
                                <input type="text" class="form-control kode-jenis-simpanan" name="kode_jenis_simpan" size="54px" placeholder="Kode Jenis Simpan" title="Kode harus diisi"/>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Jenis Simpanan</label>
                                <input type="text" class="form-control" name="nama_simpanan" size="54" placeholder="Jenis Simpanan"/>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Besar Simpanan</label>
                                <input type="text" class="form-control besar-simpanan" name="besar_simpanan" size="54" placeholder="Besar Simpanan"/>
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Tanggal Tagih</label>
                                <input type="number" class="form-control" name="tgl_tagih"  placeholder="Tanggal Tagih">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Hari Jatuh Tempo </label>
                                <input type="number" class="form-control" name="hari_jatuh_tempo" placeholder="Hari jatuh tempo" >
                            </div>
                            <div class="col-md-6 form-group">
                                <label>User Entri</label>
                                <input type="text" class="form-control" name="u_entry" size="54" value="{{ Auth::user()->name }}" readonly="">
                            </div>
                            <div class="col-md-12 form-group">
                                <button type="submit" class="form-control btn btn-sm btn-success"><i class="fa fa-save"></i> Create Jenis Simpanan</button>
                            </div> 
                       </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/cleave.min.js') }}"></script>
    
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

        var cleave = new Cleave('.kode-jenis-simpanan', {
            delimiter: '.',
            blocks: [3, 2, 3],
            uppercase: true
        });
        var cleaveBesarSimpanan = new Cleave('.besar-simpanan', {
			numeral: true,
			prefix: 'Rp ',
			noImmediatePrefix: true,
			numeralThousandsGroupStyle: 'thousand'
		});

    </script>
@endsection