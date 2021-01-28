@extends('adminlte::page')
@section('title')
	{{ $title }}
@endsection

@section('plugins.Select2', true)

@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
			<li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="">Penarikan</a></li>
			<li class="breadcrumb-item active">Buat Penarikan</li>
		</ol>
	</div>
</div>
@endsection

@section('css')
	<style>
		.img-fit{
			object-fit: cover;
			object-position: center;
			width: 100%;
			height: 230px;
		}
	</style>
@endsection

@section('content')
<form method="post" action="{{ route('penarikan-create') }}" enctype="multipart/form-data">
	{{ csrf_field() }}
	<div class="card">
		<div class="card-body">
			<div class="row">
				<div class="col-md-4">
					<div class="row">
						<div class="col-md-12 form-group" id="anggotaSelect2">
							<label for="anggotaName">Nama Anggota</label>
							<select name="kode_anggota" id="anggotaName" class="form-control" required>
                                <option value="">Pilih Salah Satu</option>
							</select>
						</div>
						<div class="col-md-12 form-group">
							<label>Jenis Simpanan</label>
							<select name="jenis_simpanan" class="form-control">
								@foreach ($jenisSimpanan as $jenis)
									<option value="{{ $jenis->kode_jenis_simpan }}">{{ $jenis->nama_simpanan }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-12 form-group">
							<label>Besar Penarikan</label>
							<input type="text" name="besar_penarikan" class="form-control penarikan" placeholder="Besar Penarikan" autocomplete="off" required>
						</div>
						<div class="col-md-12 form-group">
							<label>Password</label>
							<input type="password" name="password" class="form-control" placeholder="Password" autocomplete="off" required>
						</div>
					</div>
					<div class="form-group">
						<button type="submit" class="form-control btn btn-sm btn-success"><i class="fa fa-save"></i> Kirim</button>
					</div>
				</div>
				<div class="col-md-8 form-group" id="anggotaForm">
					<label for="detailAnggota">Detail Anggota</label>
					<div id="detailAnggota" style="background-color: #f2f2f2"></div>
				</div>
			</div>
		</div>
	</div>
</form>
@endsection

@section('js')
	<script src="{{ asset('js/cleave.min.js') }}"></script>
	<script>
		var cleave = new Cleave('.penarikan', {
			numeral: true,
			prefix: 'Rp ',
			noImmediatePrefix: true,
			numeralThousandsGroupStyle: 'thousand'
		});

        $("#anggotaName").select2({
            ajax: {
                url: '{{ route('anggota-ajax-search') }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });	

		$('#anggotaName').on('change', function ()
		{
			var selectedValue = $(this).children("option:selected").val();
			var baseURL = {!! json_encode(url('/')) !!};
			$.get(baseURL + "/penarikan/anggota/detail/" + selectedValue, function( data ) {
				$('#detailAnggota').html(data);
			});
		});	
	</script>
@endsection