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
			<li class="breadcrumb-item active">Keluar Anggota</li>
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
	<form method="post" action="{{ route('store-form-keluar-anggota') }}">
		@csrf
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-10">
                        <div class="form-group">
                            <label for="anggotaName">Nama Anggota</label>
                            <select name="kode_anggota" id="anggotaName" class="form-control" required>
                                <option value="">Pilih Salah Satu</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-sm btn-success" style="margin-top: 30px"><i class="fa fa-save"></i> Keluar Anggota</button>
                    </div>
                    <div class="col-12">
                        <div id="detailAnggota" style="background-color: #f2f2f2"></div>
                    </div>
                </div>
            </div>
        </div>
	</form>
@endsection

@section('js')
    <script>
		var baseURL = {!! json_encode(url('/')) !!};
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
    </script>
@endsection
