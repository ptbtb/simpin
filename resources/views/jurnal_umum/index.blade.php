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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css" rel="stylesheet" />
    <link href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css" rel="stylesheet" />
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
        <div class="card-header text-right">
            @can('print jkk')
                <a href="{{ route('jurnal-umum-index-jkk') }}" class="btn mt-1 btn-sm btn-info"><i class="fas fa-print"></i> Print JKK</a>
            @endcan
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>#</th>
                        <th>No</th>
                        <th>Tgl. Transaksi</th>
                        <th>Description</th>
                        <th>Jumlah</th>
                        <th>Lampiran</th>
                        <th>Oleh</th>
                        <th>Status</th>
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
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script>
        jQuery.extend( jQuery.fn.dataTableExt.oSort, {
"date-uk-pre": function ( a ) {
    var ukDatea = a.split('/');
    return (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
},

"date-uk-asc": function ( a, b ) {
    return ((a < b) ? -1 : ((a > b) ? 1 : 0));
},

"date-uk-desc": function ( a, b ) {
    return ((a < b) ? 1 : ((a > b) ? -1 : 0));
}
} );
        $.fn.dataTable.ext.errMode = 'none';
        var baseURL = {!! json_encode(url('/')) !!};

        var t = $('.table').DataTable({
        bProcessing: true,
        bServerSide: false,
        bFilter:true,
        responsive: true,
        searching: true,
        ajax: {
            url: '{{ route('jurnal-umum-list-ajax') }}',
            dataSrc: 'data',
            data: function(data){
            }
        },
        aoColumns: [
            {
                mData: 'id', visible: false,
            },
            {
                data: null, orderable: false,
                className: 'select-checkbox', defaultContent: "",
            },
            {
                mData: 'DT_RowIndex',
                className: "dt-body-center", 'name': 'DT_RowIndex',
            },
            { 
                mData: 'view_tgl_transaksi', sType: "date-uk-pre", 
                className: "dt-body-center", "name": "view_tgl_transaksi",
                mRender: function (data, type, full) {
                    if (data == null || data == '') {
                        return '-';
                    }
                    return data;
                }					
            },
            { 
                mData: 'deskripsi', sType: "string", 
                className: "dt-body-center", "name": "deskripsi"		
            },
            { 
                mData: 'total_nominal_debet_rupiah', sType: "number", 
                className: "dt-body-center", "name": "total_nominal_debet_rupiah"        
            },
            { 
                mData: 'jurnal_umum_lampirans', sType: "string", 
                className: "dt-body-center", "name": "jurnal_umum_lampirans",	
                mRender: function(data, type, full) 
                {
                    var markup = ''; 
                    var baseURL = {!! json_encode(url('/')) !!};
                    
                    for (let index = 0; index < data.length; index++) 
                    {
                        markup += '<a class="btn btn-warning btn-sm" href="' + baseURL + '/'+ data[index].lampiran + '" target="_blank"><i class="fa fa-file"></i></a>';
                        markup += '&nbsp';

                    }

                    return markup;
                }
            },
            { 
                mData: 'created_by.name', sType: "string", 
                className: "dt-body-center", "name": "created_by.name"        
            },
            { 
                mData: 'status_jurnal_umum.name', sType: "string", 
                className: "dt-body-center", "name": "status_jurnal_umum.name"        
            },
            { 
                mData: 'id', sType: "string", 
                className: "dt-body-center", "name": "id",	
                mRender: function(data, type, full) 
                {
                    var markup = ''; 
                    var baseURL = {!! json_encode(url('/')) !!};

                    markup += '<a href="' + baseURL + '/jurnal-umum/detail/' + data + '" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Detail</a> '

                    markup += '<a href="' + baseURL + '/jurnal-umum/edit/' + data + '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Edit</a> '

                    @if (Auth::user()->isAnggota())
                        if (full.status_jurnal_umum_id == {{ STATUS_JURNAL_UMUM_MENUNGGU_KONFIRMASI }})
                        {
                            markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_KONFIRMASI }}" data-status="{{ STATUS_JURNAL_UMUM_DIBATALKAN }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Cancel</a>';
                        }
                        else
                        {
                            markup += '-';
                        }
                    @else
                        @can('approve jurnal umum')
                            if (full.status_jurnal_umum_id == {{ STATUS_JURNAL_UMUM_MENUNGGU_KONFIRMASI }})
                            {
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_KONFIRMASI }}" data-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_SPV }}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Terima</a>';
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_KONFIRMASI }}" data-status="{{ STATUS_JURNAL_UMUM_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                            }
                            else if (full.status_jurnal_umum_id == {{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_SPV }})
                            {
                                @can('approve jurnal umum spv')
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_SPV }}" data-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_ASMAN }}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_SPV }}" data-status="{{ STATUS_JURNAL_UMUM_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                                @endcan
                            }
                            else if (full.status_jurnal_umum_id == {{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_ASMAN }})
                            {
                                // temporary skip manager, bendahara, ketua
                                // markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_ASMAN }}" data-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_MANAGER }}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_ASMAN }}" data-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_PEMBAYARAN }}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_ASMAN }}" data-status="{{ STATUS_JURNAL_UMUM_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                            }
                            else if (full.status_jurnal_umum_id == {{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_MANAGER }})
                            {
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_MANAGER }}" data-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_BENDAHARA }}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_MANAGER }}" data-status="{{ STATUS_JURNAL_UMUM_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                            }
                            else if (full.status_jurnal_umum_id == {{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_BENDAHARA }})
                            {
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_BENDAHARA }}" data-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_KETUA}}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_BENDAHARA }}" data-status="{{ STATUS_JURNAL_UMUM_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                            }
                            else if (full.status_jurnal_umum_id == {{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_KETUA }})
                            {
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_KETUA }}" data-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_PEMBAYARAN}}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_APPROVAL_KETUA }}" data-status="{{ STATUS_JURNAL_UMUM_DITOLAK }}" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-times"></i> Tolak</a>';
                            }
                            else if (full.status_jurnal_umum_id == {{ STATUS_JURNAL_UMUM_MENUNGGU_PEMBAYARAN }})
                            {
                                @can('bayar jurnal umum')
                                    if (full.status_jkk == 1)
                                    {
                                        markup += '<a data-id="'+data+'" data-old-status="{{ STATUS_JURNAL_UMUM_MENUNGGU_PEMBAYARAN }}" data-status="{{ STATUS_JURNAL_UMUM_DITERIMA}}" class="text-white btn btn-sm mt-1 btn-success btn-approval"><i class="fas fa-check"></i> Setuju</a>';
                                    }
                                    else
                                    {
                                        markup += 'JKK Belum di Print';

                                    }
                                @endcan
                            }
                        @endcan
                    @endif

                    return markup;
                }
            },
        ],
        columnDefs: [
            { "targets": 0,"searchable": false, "orderable": false, 'checkboxes' : { 'selectRow': true } },
            { "targets": 1,"searchable": false, "orderable": false },
            { "targets": 2,"searchable": false, "orderable": false },
            { "targets": 3,"searchable": true, "orderable": true },
            { "targets": 4,"searchable": true, "orderable": true },
            { "targets": 5,"searchable": false, "orderable": false },
            { "targets": 6,"searchable": false, "orderable": false },
            { "targets": 7,"searchable": true, "orderable": true },
            { "targets": 8,"searchable": false, "orderable": false },
        ],
        dom: 'lfBrtip',
        buttons: [
            'selectAll',
            'selectNone',
        ],
        select: {
            style: 'multi',
            selector: 'td:first-child'
        },
        fnInitComplete: function (oSettings, json) {

            var _that = this;

            this.each(function (i) {
                $.fn.dataTableExt.iApiIndex = i;
                var $this = this;
                var anControl = $('input', _that.fnSettings().aanFeatures.f);
                // anControl
                //     .unbind('keyup search input')
                //     .bind('keypress', function (e) {
                //         if (e.which == 13) {
                //             $.fn.dataTableExt.iApiIndex = i;
                //             _that.fnFilter(anControl.val());
                //         }
                //     });
                return this;
            });

            return this;
        },
    });

    $(document).on('click', '.btn-approval', function ()
    {
        var id = $(this).data('id');
        var status = $(this).data('status');
        var old_status = $(this).data('old-status');
        var url = '{{ route("jurnal-umum-update-status") }}';

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            input: 'password',
            inputAttributes: {
                name: 'password',
                placeholder: 'Password',
                required: 'required',
                validationMessage:'Password required',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            }
        }).then((result) => {
            if (result.isConfirmed) {
                var password = result.value;
                var formData = new FormData();
                var token = "{{ csrf_token() }}";
                formData.append('_token', token);
                formData.append('id', id);
                formData.append('status', status);
                formData.append('password', password);
                formData.append('old_status', old_status);
                // getting selected checkboxes kode ambil(s)
                var ids_array = t
                                .rows({ selected: true })
                                .data()
                                .pluck('id')
                                .toArray();
                if (ids_array.length != 0)
                {
                    // append ids array into form
                    formData.append('ids', JSON.stringify(ids_array));
                }
                else
                {
                    formData.append('ids', '['+id+']');
                }
                $.ajax({
                    type: 'post',
                    url: url,
                    data: formData,
                    contentType: false,
                    processData: false,
                success: function(data) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Your has been changed',
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                },
                error: function(error){
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: error.responseJSON.message,
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        }
                    });
                }
                })
            }
        })
        
    });

    </script>
@endsection