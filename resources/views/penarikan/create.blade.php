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
						@if (Auth::user()->isAnggota())
							<div class="col-md-12 form-group" id="anggotaSelect2">
								<label for="anggotaName">Nama Anggota</label>
								<input type="text" name="nama_anggota" id="namaAnggota" class="form-control" value="{{ Auth::user()->anggota->nama_anggota }}" readonly>
								<input type="hidden" name="kode_anggota" id="kodeAnggota" value="{{ Auth::user()->anggota->kode_anggota }}">
							</div>
						@else
							<div class="col-md-12 form-group" id="anggotaSelect2">
								<label for="anggotaName">Nama Anggota</label>
								<select name="kode_anggota" id="anggotaName" class="form-control" required>
									<option value="">Pilih Salah Satu</option>
								</select>
							</div>
						@endif
						<div class="col-md-12 form-group">
							<label>Jenis Simpanan</label>
							<select id="selectJenisSimpanan" name="jenis_simpanan[]" multiple="multiple" class="form-control" autocomplete="off">
								@foreach ($jenisSimpanan as $jenis)
									<option value="{{ $jenis->kode_jenis_simpan }}" data-id="{{ $jenis->kode_jenis_simpan }}">{{ $jenis->nama_simpanan }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-12" id="besar-penarikan">
							{{-- <label>Besar Penarikan</label>
							<input type="text" name="besar_penarikan" class="form-control penarikan" placeholder="Besar Penarikan" autocomplete="off" required> --}}
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
	{{-- <script src="{{ asset('js/cleave.min.js') }}"></script> --}}
    <script src="{{ asset('js/collect.min.js') }}"></script>
	<script>
		var baseURL = {!! json_encode(url('/')) !!};
        var jenisSimpanan = collect(@json($jenisSimpanan));
		// var cleave = new Cleave('.penarikan', {
		// 	numeral: true,
		// 	prefix: 'Rp ',
		// 	noImmediatePrefix: true,
		// 	numeralThousandsGroupStyle: 'thousand'
		// });

		$(document).ready(function ()
		{
			@if(Auth::user()->isAnggota())
				var kode_anggota = $('#kodeAnggota').val();
				$.get(baseURL + "/penarikan/anggota/detail/" + kode_anggota, function( data ) {
					$('#detailAnggota').html(data);
				});
			@endif
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
			$.get(baseURL + "/penarikan/anggota/detail/" + selectedValue, function( data ) {
				$('#detailAnggota').html(data);
			});
		});

        $('#selectJenisSimpanan').select2({
            placeholder: 'Pilih Jenis Simpanan'
        });

        // Event ketika select jenis pinjaman berubah
        $(document).on('change','#selectJenisSimpanan', function ()
        {
            var selected = $('#selectJenisSimpanan').val();
            var content = '';
            var parentId = '#besar-penarikan';
            selected.forEach(value => {
                var childId = '#besar-penarikan-'+value;

                var namaSimpanan = jenisSimpanan.where('kode_jenis_simpan', value).first().nama_simpanan;
                namaSimpanan = namaSimpanan.toLowerCase().replace(/\b[a-z]/g, function(letter) {
                    return letter.toUpperCase();
                });

                content = content + '<div class="form-group" id="' + childId + '">' +
                                '<label>Besar Penarikan '+ namaSimpanan +'</label>' +
							    '<input type="text" name="besar_penarikan['+value+']" class="form-control toRupiah penarikan" placeholder="Besar Penarikan" autocomplete="off" required>' +
                            '</div>';
            });
            $(parentId).html(content);

            $('.toRupiah').each(function (index)
            {
                var value = $(this).val();
                if (value != null && value != '')
                {
                    $(this).val(toRupiah(value));
                }
            });

            $('.toRupiah').on('keyup', function () {
                var val = $(this).val();
                val = val.replace(/[^\d]/g, "",'');
                $(this).val(toRupiah(val));
            });
        })

        function toRupiah(number)
        {
            var stringNumber = number.toString();
            var length = stringNumber.length;
            var temp = length;
            var res = "Rp ";
            for (let i = 0; i < length; i++) {
                res = res + stringNumber.charAt(i);
                temp--;
                if (temp%3 == 0 && temp > 0)
                {
                    res = res + ".";
                }
            }
            return res;
        }

	</script>
@endsection
