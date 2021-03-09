@extends('adminlte::page')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
<div class="row">
	<div class="col-6"><h4>{{ $title }}</h4></div>
	<div class="col-6">
		<ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="">Jurnal Umum</a></li>
			<li class="breadcrumb-item active">List Jurnal Umum</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Datatables', true)

@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .btn-sm{
            font-size: .8rem;
        }

        .box-custom{
            border: 1px solid black;
            border-radius: 0;
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Code</th>
                        <th>Nominal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody> 
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script>
        $.fn.dataTable.ext.errMode = 'none';
        var baseURL = {!! json_encode(url('/')) !!};

        var t = $('.table').DataTable({
        processing: true,
        serverside: true,
        responsive: true,
        order: [[ 1, "desc" ]],
        ajax: {
            url: '{{ route('jurnal-umum-list-ajax') }}',
            dataSrc: 'data',
            data: function(data){
            }
        },
        aoColumns: [
            { 
                mData: null		
            },
            { 
                mData: 'code.CODE', sType: "string", 
                className: "dt-body-center", "name": "code.CODE",
                mRender: function (data, type, full) {
                    if (data == null || data == '') {
                        return '-';
                    }
                    return data;
                }					
            },
            { 
                mData: 'nominal_rupiah', sType: "string", 
                className: "dt-body-center", "name": "nominal_rupiah"		
            },
            { 
                mData: 'id', sType: "string", 
                className: "dt-body-center", "name": "id",	
                mRender: function(data, type, full) 
                {
                    var markup = ''; 
                    var baseURL = {!! json_encode(url('/')) !!};

                    markup += '<a href="' + baseURL + '/jurnal-umum/edit/' + data + '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a> '

                    return markup;
                }
            },
        ]
    });

    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();

    </script>
@endsection