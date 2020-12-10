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
            <li class="breadcrumb-item"><a href="">Simpanan</a></li>
			<li class="breadcrumb-item active">Kartu Simpanan</li>
		</ol>
	</div>
</div>
@endsection

@section('css')
@endsection

@section('content')
@if (\Auth::user()->isAnggota())
	<div id="exportButton" class="d-none text-right my-2">
		@if(\Auth::user()->isAnggota())
			<a class="btn btn-sm btn-info" id="pdfButton"><i class="fas fa-download"></i> Export PDF</a>
		@else
			<a href="" class="btn btn-sm btn-info" id="pdfButton"><i class="fas fa-download"></i> Export PDF</a>
			<a href="" class="btn btn-sm btn-warning" id="excelButton"><i class="fas fa-download"></i> Export Excel</a>
		@endif
	</div>
	<div id="detailAnggota" class="mb-2" style="background-color: #f2f2f2"></div>
@else
	<form method="post" action="{{ route('user-create') }}" enctype="multipart/form-data">
		{{ csrf_field() }}
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="row">
							@if ($request->kode_anggota)
								<div class="col-md-12 form-group">
									<label for="anggotaName">Nama Anggota</label>
									<select name="kode_anggota" id="anggotaName" class="form-control" required>
										<option value="{{ $anggota->kode_anggota }}">{{ $anggota->nama_anggota }}</option>
									</select>
								</div>
							@else
								<div class="col-md-6 form-group" id="anggotaSelect2">
									<label for="anggotaName">Nama Anggota</label>
									<select name="kode_anggota" id="anggotaName" class="form-control">
									</select>
								</div>
							@endif
							<div class="col-12 form-group mt-2 " id="anggotaForm">
								<label for="detailAnggota">Preview Kartu Simpanan</label>
								<div id="exportButton" class="d-none text-right my-2">
									@if(\Auth::user()->isAnggota())
										<a class="btn btn-sm btn-info" id="pdfButton"><i class="fas fa-download"></i> Export PDF</a>
									@else
										<a href="" class="btn btn-sm btn-info" id="pdfButton"><i class="fas fa-download"></i> Export PDF</a>
										<a href="" class="btn btn-sm btn-warning" id="excelButton"><i class="fas fa-download"></i> Export Excel</a>
									@endif
								</div>
								<div id="detailAnggota" style="background-color: #f2f2f2"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
@endif
@endsection

@section('js')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
	<script>
		$(document).ready(function ()
		{
			@if ($request->kode_anggota)
				var selectedValue = $('#anggotaName').children("option:selected").val();
				var baseURL = {!! json_encode(url('/')) !!};
				$.get(baseURL + "/simpanan/card/view/" + selectedValue, function( data ) {
					$('#detailAnggota').html(data);
				});
				var urlExport = baseURL + "/simpanan/card/download";
				$('#exportButton').removeClass('d-none');
				@if(\Auth::user()->isAnggota())
					$('#pdfButton').attr('href', urlExport+"/pdf/"+selectedValue);
				@else
					$('#pdfButton').attr('href', urlExport+"/pdf/"+selectedValue);
					$('#excelButton').attr('href', urlExport+"/excel/"+selectedValue);
				@endif
			@endif
		});
		@if(\Auth::user()->isAnggota())
			$(document).ready(function () {
				var selectedValue = '{{ \Auth::user()->anggota->kode_anggota }}';
				var baseURL = {!! json_encode(url('/')) !!};
				$.get(baseURL + "/simpanan/card/view/" + selectedValue, function( data ) {
					$('#detailAnggota').html(data);
				});
				var urlExport = baseURL + "/simpanan/card/download";
				$('#exportButton').removeClass('d-none');
				@if(\Auth::user()->isAnggota())
					$('#pdfButton').attr('href', urlExport+"/pdf/"+selectedValue);
				@else
					$('#pdfButton').attr('href', urlExport+"/pdf/"+selectedValue);
					$('#excelButton').attr('href', urlExport+"/excel/"+selectedValue);
				@endif
			});
		@endif
		
		function readURL(input, previewContainer) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#' + previewContainer).attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
		}
		
		$('#photoButton').on('change', '.btn-file :file', function () {
            readURL(this, 'photoPreview');
        });

		@if (!$request->kode_anggota)
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
		@endif

		$('#anggotaName').on('change', function ()
		{
			var selectedValue = $(this).children("option:selected").val();
			var baseURL = {!! json_encode(url('/')) !!};
			$.get(baseURL + "/simpanan/card/view/" + selectedValue, function( data ) {
				$('#detailAnggota').html(data);
			});
            var urlExport = baseURL + "/simpanan/card/download";
            $('#exportButton').removeClass('d-none');
			@if(\Auth::user()->isAnggota())
				$('#pdfButton').attr('href', urlExport+"/pdf/"+selectedValue);
			@else
				$('#pdfButton').attr('href', urlExport+"/pdf/"+selectedValue);
				$('#excelButton').attr('href', urlExport+"/excel/"+selectedValue);
			@endif
		});		
	</script>
@endsection