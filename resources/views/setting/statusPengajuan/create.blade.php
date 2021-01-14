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
			<li class="breadcrumb-item"><a href="{{ route('status-pengajuan-list') }}">Status Pengajuan</a></li>
			<li class="breadcrumb-item active">Create</li>
		</ol>
	</div>
</div>
@endsection

@section('content')
    <form action="{{ route('status-pengajuan-add') }}" method="post" enctype="multipart/form-data">
        {{csrf_field()}}
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <label>Nama Status Pengajuan</label>
                                <input type="text" class="form-control" name="name" size="54px" placeholder="Nama Status Pengajuan" />
                            </div>
                            <div class="col-md-12 form-group">
                                <label>Batas Pengajuan</label>
                                <input type="text" class="form-control nominal-rupiah" name="batas_pengajuan" size="54" placeholder="Batas pengajuan"/>
                            </div>
                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Create Status Pengajuan</button>
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
        var cleaveBesarSimpanan = new Cleave('.nominal-rupiah', {
			numeral: true,
			prefix: 'Rp ',
			noImmediatePrefix: true,
			numeralThousandsGroupStyle: 'thousand'
		});

    </script>
@endsection