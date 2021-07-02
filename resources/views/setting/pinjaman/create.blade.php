@extends('adminlte::page')
@section('title')
    {{ $title }}
@endsection

@section('plugins.Datatables', true)

@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
			<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
			<li class="breadcrumb-item"><a href="{{ route('jenis-pinjaman-list') }}">Jenis Pinjaman</a></li>
			<li class="breadcrumb-item active">Create</li>
		</ol>
	</div>
</div>
@endsection

@section('content')
<form action="{{ route('jenis-pinjaman-add') }}" method="post">
    {{csrf_field()}}
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <label>Tanggal Entri : <?php echo date("d M Y"); ?></label>
                </div>
                <div class="form-group col-md-6">
                    <label>Kode Jenis Pinjaman</label>
                    <input type="text" class="form-control kode-jenis-pinjaman" name="kode_jenis_pinjam" size="54" placeholder="Kode Jenis Pinjaman" />
                </div>
                <div class="form-group col-md-6">
                    <label>Tipe Jenis Pinjaman</label>
                    <select name="tipe_jenis_pinjaman_id" class="form-control" required id="tipeJenisPinjamanOption">
						<option value="">Choose One</option>
						@foreach ($tipe_jenis_pinjaman as $type)
							<option value="{{ $type->id }}">{{ $type->name }}</option>
						@endforeach
					</select>
                </div>
                <div class="form-group col-md-6">
                    <label>Kategori Jenis Pinjaman</label>
                    <select name="kategori_jenis_pinjaman_id" class="form-control" required id="kategoriJenisPinjamanOption">
						<option value="">Choose One</option>
						@foreach ($kategori_jenis_pinjaman as $category)
							<option value="{{ $category->id }}">{{ $category->name }}</option>
						@endforeach
					</select>
                </div>
                <div class="form-group col-md-6">
                    <label>Jenis Pinjaman</label>
                    <input type="text" class="form-control" name="nama_pinjaman" size="54" placeholder="Jenis Pinjaman"/>
                </div>
                <div class="form-group col-md-6">
                    <label>Lama Angsuran </label>
                    <input type="number" class="form-control" name="lama_angsuran" placeholder="Lama Angsuran">
                </div>
                <div class="form-group col-md-6">
                    <label>Maksimal Pinjaman</label>
                    <input type="number" step="any" class="form-control" name="maks_pinjam" size="54" placeholder="Maksimal Pinjaman"/>
                </div>
                <div class="form-group col-md-6">
                    <label>Bunga</label>
                    <input type="number" step="any" step="any" class="form-control" name="bunga" size="54" placeholder="Bunga"/>
                </div>
                <div class="form-group col-md-6">
                    <label>Asuransi</label>
                    <input type="number" step="any" step="any" class="form-control" name="asuransi" placeholder="Asuransi"/>
                </div>
                <div class="form-group col-md-6">
                    <label>Biaya Admin</label>
                    <input type="text" class="form-control nominal-rupiah" name="biaya_admin" size="54" placeholder="Biaya Admin"/>
                </div>
                <div class="form-group col-md-6">
                    <label>Provisi</label>
                    <input type="number" step="any" class="form-control" name="provisi" size="54" placeholder="Provisi"/>
                </div>
                <div class="form-group col-md-6">
                    <label>Jasa</label>
                    <input type="number" step="any" class="form-control" name="jasa" placeholder="Jasa" />
                </div>
                <div class="form-group col-md-6">
                    <label>Jasa Pelunasan Dipercepat</label>
                    <input type="number" step="any" class="form-control" name="jasa_pelunasan_dipercepat" placeholder="Jasa Pelunasan Dipercepat" />
                </div>
                <div class="form-group col-md-6">
                    <label>Minimal Angsur Pelunasan</label>
                    <input type="number" step="any" class="form-control" name="minimal_angsur_pelunasan" placeholder="Minimal Angsur Pelunasan" />
                </div>
                <div class="form-group col-md-6">
                    <label>User Entri</label>
                    <input type="text" class="form-control" name="u_entry" size="54" value="{{ Auth::user()->name }}" readonly />
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Create Jenis Pinjaman</button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('css')
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

    $('.nominal-rupiah').toArray().forEach((field) => {
        new Cleave(field, {
            numeral: true,
            prefix: 'Rp ',
            noImmediatePrefix: true,
            numeralThousandsGroupStyle: 'thousand'
        })
    })

    var cleave = new Cleave('.kode-jenis-pinjaman', {
        delimiter: '.',
        blocks: [3, 2, 3],
        uppercase: true
    });

</script>
@endsection
