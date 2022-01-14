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
			<li class="breadcrumb-item active">{{ $title }}</li>
		</ol>
	</div>
</div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.Select2', true)

@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
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

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha256-siyOpF/pBWUPgIcQi17TLBkjvNgNQArcmwJB8YvkAgg=" crossorigin="anonymous" />
<style>

.btn-sm{
    font-size: .8rem;
}
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
         <form action="{{ route('auditJurnal') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Tipe Jurnal</label>
                        {!! Form::select('id_tipe_jurnal', $tipeJurnal, $request->id_tipe_jurnal, ['class' => 'form-control', 'placeholder' => 'All']) !!}
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Nomor</label>
                        <input type="text" name="serial_number" id="serial_number" class="form-control" placeholder="Nomor Transaksi" autocomplete="off" value="{{ old('serial_number', $request->serial_number) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>AKUN</label>
                        <input type="text" name="code" id="code" class="form-control" placeholder="COA" autocomplete="off" value="{{ old('code', $request->code) }}">
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Keterangan</label>
                        <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Keterangan" autocomplete="off" value="{{ old('keterangan', $request->keterangan) }}">
                    </div>
                    <div class="col-md-3">
                        <label>Dari</label>
                        <input class="form-control datepicker" placeholder="dd-mm-yyyy" id="from" name="from" value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y') }}" autocomplete="off" />
                    </div>
                    <div class="col-md-3">
                        <label>Sampai</label>
                        <input class="form-control datepicker" placeholder="mm-yyyy" id="to" name="to" value="{{ Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y') }}" autocomplete="off" />
                    </div>
                    <div class="col-md-12 form-group text-center">
                        <button type="submit" class="btn btn-sm btn-success"><i class="fa fa-filter"></i> Filter</button>
                        <a href="{{ route('jurnal-export-excel',['id_tipe_jurnal'=>$request->id_tipe_jurnal,'serial_number'=>$request->serial_number,'code'=>$request->code,'from'=>Carbon\Carbon::createFromFormat('d-m-Y', $request->from)->format('d-m-Y'),'to'=>Carbon\Carbon::createFromFormat('d-m-Y', $request->to)->format('d-m-Y'),'keterangan'=>$request->keterangan]) }}" class="btn btn-success"><i class="fa fa-download"></i> export Excel</a>
                    </div>
                </div>
            </form>
        
    </div>

    <div class="card-body table-responsive">
        <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nomor</th>
                        <th>No Anggota</th>
                        <th>Akun Debet</th>
                        <th style="width: 10%">Debet</th>
                        <th>Akun Kredit</th>
                        <th style="width: 10%">Kredit</th>
                        <th>Keterangan</th>
                        <th>Tanggal</th>
                        <th>Action</th>
                        
                    </tr>
                </thead>
                <tfoot>
                <tr>
                <th colspan="4" style="text-align:right">Total:</th>
                <th id="totaldebet" style="text-align:right"></th>
                <th></th>
                <th id="totalkredit" style="text-align:right"></th>
                <th colspan="3"></th>
            </tr>
        </tfoot>
            </table>
        </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha256-bqVeqGdJ7h/lYPq6xrPv/YGzMEb6dNxlfiTUHSgRCp8=" crossorigin="anonymous"></script>
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script>
    var table = $('.table').DataTable({
                bProcessing: true,
                bServerSide: true,
                bSortClasses: false,
                ordering: false,
                searching: false,
                responsive: true,
                ajax: {
                    url: '{{ route('audit-jurnal-ajax') }}',
                    dataSrc: 'data',
                    data: function(data){
                        @if(isset($request->id_tipe_jurnal)) data.id_tipe_jurnal = '{{ $request->id_tipe_jurnal }}'; @endif
                        
                        var serial_number = '{{ $request->serial_number }}';
                        data.serial_number = serial_number;
                        var code = '{{ $request->code }}';
                        data.code = code;

                        var keterangan = '{{ $request->keterangan }}';
                        data.keterangan = keterangan; 
                        var from = '{{ $request->from }}';
                        data.from = from;
                        var to = '{{ $request->to }}';
                        data.to = to;
                    }
                },
                aoColumns: [
                    {
                    data: null, orderable: false,
                    className: 'select-checkbox', defaultContent: "",
                    },
                    {
                        mData: 'jurnalable_view', sType: "string",
                        className: "dt-body-center", "name": "jurnalable_view",
                        mRender: function(data, type, full)
                        {

                            if(data)
                            {
                                if (full.id_tipe_jurnal==2 && full.jurnalable_type=="App\\Models\\Pinjaman"){
                                     return data.serial_number_kredit_view;
                                }
                                return data.serial_number_view;
                            }else{
                                return '';
                            }
                        }
                    },
                    {
                        mData: 'kode_anggota_view', sType: "string",
                        className: "dt-body-center", "name": "kode_anggota_view",
                       
                    },
                    
                    {
                        mData: 'akun_debet', sType: "string",
                        className: "dt-body-center", "name": "akun_debet",
                    },
                    {
                        mData: 'nominal_rupiah_debet', sType: "string",
                        className: "dt-body-center", "name": "debet"
                    },
                    {
                        mData: 'akun_kredit', sType: "string",
                        className: "dt-body-center", "name": "akun_kredit"
                    },
                    {
                        mData: 'nominal_rupiah_kredit', sType: "string",
                        className: "dt-body-center", "name": "kredit"
                    },
                    {
                        mData: 'keterangan', sType: "string",
                        className: "dt-body-center", "name": "keterangan",
                    },
                    {
                        mData: 'tgl_transaksi', sType: "string",
                        className: "dt-body-center", "name": "tgl_transaksi",
                    },
                    {
                    mData: 'id', sType: "string",
                    className: "dt-body-center", "name": "id",
                    mRender : function(data, type, full)
                    {
                        var markup = '';
                        markup += '<a data-id="'+data+'" class="text-white btn btn-sm mt-1 btn-danger btn-approval"><i class="fas fa-trash"></i> Delete</a>';
                        return markup;

                    }
                },
                    /*{
                        mData: 'created_by.name', sType: "string",
                        className: "dt-body-center", "name": "created_by.name",
                    },*/
                ],
                dom: 'lBrtip',
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
                    anControl
                        .unbind('keyup search input')
                        .bind('keypress', function (e) {
                            if (e.which == 13) {
                                $.fn.dataTableExt.iApiIndex = i;
                                _that.fnFilter(anControl.val());
                            }
                        });
                    return this;
                });

                return this;
            },
                drawCallback:function(settings)
    {
      $('#totaldebet').html(toRupiah(settings.json.totaldebet));
      $('#totalkredit').html(toRupiah(settings.json.totalkredit));
    }
                
            });

    $(document).on('click', '.btn-approval', function ()
        {
            var id = $(this).data('id');
            var url = '{{ route("auditJurnal-delete") }}';
            
            // files is mandatory when status pengajuan pinjaman diterima
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
                        formData.append('password', password);
                        // getting selected checkboxes kode ambil(s)
                        var ids_array = table
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

    

    $('input.datepicker').bind('keyup keydown keypress', function (evt) {
        return true;
    });
    $.fn.modal.Constructor.prototype._enforceFocus = function() {};
        $('#from').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd-mm-yyyy'
        });
        $('#to').datepicker({
            uiLibrary: 'bootstrap4',
            format: 'dd-mm-yyyy'
        });
    function toRupiah(number)
        {
            var stringNumber = number.toString();
            var length = stringNumber.length;
            var temp = length;
            var res = "";
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