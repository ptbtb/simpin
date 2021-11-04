@extends('adminlte::page')

@section('title')
    {{ $title }}
@endsection

@section('content_header')
    <div class="row">
        <div class="col-6">
            <h4>{{ $title }}</h4>
        </div>
        <div class="col-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item">Jkk Printed</li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Datatables', true)
@section('plugins.SweetAlert2', true)
@section('plugins.Select2', true)

@section('css')
    <style>
        .btn-sm {
            font-size: .8rem;
        }

    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <strong>JKK Header</strong>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <tbody>
                    <tr>
                        <th>No</th>
                        <th>:</th>
                        <td>{{ $jkk->jkk_number }}</td>
                        <th>Printed By</th>
                        <th>:</th>
                        <td>{{ $jkk->printedBy->name }}</td>
                    </tr>
                    {{-- <tr>
                        <th>Credited Transfer</th>
                        <th>:</th>
                        <th>Debited Amount</th>
                        <th>:</th>
                    </tr> --}}
                </tbody>
            </table>
        </div>
        <div class="card-header">
            <strong>JKK Detail</strong>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Trx Reference</th>
                        <th>Credited Account Name</th>
                        <th>Bank Name Country</th>
                        <th>Credited Transfer</th>
                        <th>Remark</th>
                        <th>Debited Account</th>
                        <th>Debited Amount</th>
                        @if ($jkk->konfirmasi_pembayaran)
                            <th>
                                Action
                            </th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if ($listPengajuan->count())
                        @foreach ($listPengajuan as $pengajuan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $pengajuan->kode_pengajuan }}</td>
                                <td>{{ strtoupper($pengajuan->anggota->nama_anggota) }}</td>
                                <td>{{ strtoupper($settings[COMPANY_SETTING_BANK_NAME]) }}</td>
                                <td>
                                    @if ($pengajuan->pinjaman)
                                        {{ "Rp " . number_format($pengajuan->pinjaman->pinjamanDitransfer,2,',','.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ strtoupper($pengajuan->jenisPinjaman->nama_pinjaman) }}</td>
                                <td>{{ $settings[COMPANY_SETTING_BANK_ACCOUNT] }}</td>
                                <td>
                                    @if ($pengajuan->pinjaman)
                                        {{ "Rp " . number_format($pengajuan->pinjaman->pinjamanDitransfer,2,',','.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                @if ($pengajuan->menungguPembayaran())
                                    <td>
                                        <a data-id="{{ $pengajuan->id }}" data-kode-pengajuan="{{ $pengajuan->kode_pengajuan }}" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}" class="text-white btn btn-sm mt-1 btn-success btn-konfirmasi">Konfirmasi Pembayaran</a>
                                    </td>
                                @else
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        @foreach ($listPenarikan as $penarikan)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $penarikan->kode_anggota }}</td>
                                <td>{{ strtoupper($penarikan->anggota->nama_anggota) }}</td>
                                <td>{{ strtoupper($settings[COMPANY_SETTING_BANK_NAME]) }}</td>
                                <td>{{ "Rp " . number_format($penarikan->besar_ambil,2,',','.') }}</td>
                                <td>PENARIKAN {{ strtoupper($penarikan->jenisSimpanan->where('kode_jenis_simpan', $penarikan->code_trans)->first()->nama_simpanan) }}</td>
                                <td>{{ $settings[COMPANY_SETTING_BANK_ACCOUNT] }}</td>
                                <td>{{ "Rp " . number_format($penarikan->besar_ambil,0,',','.') }}</td>
                                @if ($penarikan->menungguPembayaran())
                                    <td>
                                        <a data-id="{{ $penarikan->kode_ambil }}" data-status="{{ STATUS_PENGAMBILAN_DITERIMA }}" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN }}" class="text-white btn btn-sm btn-success btn-konfirmasi">Konfirmasi Pembayaran</a>
                                    </td>
                                @else
                                    <td></td>
                                @endif
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @if ($listPengajuan->count())
        <div id="my-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Detail Pinjaman</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-detail"></div>
                        <hr>
                        <form enctype="multipart/form-data" id="formKonfirmasi">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Tanggal Pembayaran</label>
                                    <input id="tgl_transaksi" type="date" name="tgl_transaksi" class="form-control" placeholder="yyyy-mm-dd" required value="{{ Carbon\Carbon::today()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Jenis Akun</label>
                                    <select name="jenis_akun" id="jenisAkun" class="form-control select2" required>
                                        <option value="1">KAS</option>
                                        <option value="2" selected>BANK</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Upload Bukti Pembayaran</label>
                                    <input type="file" name="bukti_pembayaran" id="buktiPembayaran" class="form-control">
                                    {{-- <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="buktiPembayaran" name="bukti_pembayaran" style="cursor: pointer">
                                        <label class="custom-file-label" for="customFile">Choose File</label>
                                    </div> --}}
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Akun</label>
                                    <select name="id_akun_debet" id="code" class="form-control select2" required>
                                        <option value="" selected disabled>Pilih Akun</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">

                            <a data-id="" data-status="{{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }}" data-old-status="{{ STATUS_PENGAJUAN_PINJAMAN_MENUNGGU_PEMBAYARAN }}"class="text-white btn mt-1 btn-sm btn-success btn-approval">Bayar</a>

                        <button type="button" class="btn mt-1 btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>  
    @else
        <div id="my-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Detail Penarikan</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-detail"></div>
                        <hr>
                        <form enctype="multipart/form-data" id="formKonfirmasi">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Tanggal Pembayaran</label>
                                    <input id="tgl_transaksi" type="date" name="tgl_transaksi" class="form-control" placeholder="yyyy-mm-dd" required value="{{ Carbon\Carbon::today()->format('Y-m-d') }}">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Jenis Akun</label>
                                    <select name="jenis_akun" id="jenisAkun" class="form-control select2" required>
                                        <option value="1">KAS</option>
                                        <option value="2" selected>BANK</option>
                                    </select>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label>Akun</label>
                                    <select name="id_akun_debet" id="code" class="form-control select2" required>
                                        <option value="" selected disabled>Pilih Akun</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label>Upload Bukti Pembayaran</label>
                                    <input type="file" name="bukti_pembayaran" id="buktiPembayaran" class="form-control">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                            <a data-id="" data-status="{{ STATUS_PENGAMBILAN_DITERIMA }}" data-old-status="{{ STATUS_PENGAMBILAN_MENUNGGU_PEMBAYARAN }}"class="text-white btn btn-sm btn-success btn-approval">Bayar</a>
                        
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>      
    @endif
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        var baseURL = {!! json_encode(url('/')) !!};        
        $.fn.modal.Constructor.prototype._enforceFocus = function() {};
        @if($listPengajuan->count())
            $(document).on('click', '.btn-approval', function ()
            {
                var id = $(this).data('id');
                var status = $(this).data('status');
                var old_status = $(this).data('old-status');
                var tgl_transaksi = $('#tgl_transaksi').val();
                var url = '{{ route("pengajuan-pinjaman-update-status") }}';

                var files = $('#buktiPembayaran')[0].files;
                var id_akun_debet = $('#code').val();

                // files is mandatory when status pengajuan pinjaman diterima
                if(status == {{ STATUS_PENGAJUAN_PINJAMAN_DITERIMA }} && files[0] == undefined)
                {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Wajib upload bukti pembayaran!',
                    });
                }
                else
                {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        html: '<div class="form-group text-left"><label>Keterangan</label><textarea placeholder="Keterangan" name="keterangan" id="keterangan" class="form-control"></textarea></div>',
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
                            var keterangan = $('#keterangan').val();
                            formData.append('_token', token);
                            formData.append('id', id);
                            formData.append('status', status);
                            formData.append('bukti_pembayaran', files[0]);
                            formData.append('password', password);
                            formData.append('id_akun_debet', id_akun_debet);
                            formData.append('old_status', old_status);
                            formData.append('tgl_transaksi', tgl_transaksi);
                            formData.append('keterangan', keterangan);
                            formData.append('ids', JSON.stringify([id]));
                            // getting selected checkboxes kode ambil(s)
                            /*var ids_array = table
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
                            }*/
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
                }
            });

            $(document).on('click', '.btn-konfirmasi', function ()
            {
                var id = $(this).data('id');
                var kodePengajuan = $(this).data('kode-pengajuan');
                var action = $(this).data('action');
                var url = baseURL + '/pinjaman/detail-pembayaran/'+kodePengajuan;

                $.get(url, function( data ) {
                    $('#my-modal .form-detail').html(data);
                    $('.btn-approval').data('id', id);
                    $('#my-modal').modal({
                        backdrop: false
                    });
                    $('#my-modal').modal('show');
                });

                $('#jenisAkun').trigger( "change" );
            });
        @else
            $(document).on('click', '.btn-konfirmasi', function ()
            {
                var id = $(this).data('id');
                var status = $(this).data('status');
                var old_status = $(this).data('old-status');
                var action = $(this).data('action');
                var url = baseURL + '/penarikan/detail-transfer/'+id;

                $.get(url, function( data ) {
                    $('#my-modal .form-detail').html(data);
                    $('.btn-approval').data('id', id);
                    $('#my-modal').modal({
                        backdrop: false
                    });
                    $('#my-modal').modal('show');
                });
                $('#jenisAkun').trigger( "change" );
            });

            $(document).on('click','.btn-approval', function ()
            {
                var id = $(this).data('id');
                var status = $(this).data('status');
                var old_status = $(this).data('old-status');
                var tgl_transaksi = $('#tgl_transaksi').val();
                var url = '{{ route("penarikan-update-status") }}';

                var files = $('#buktiPembayaran')[0].files;
                var id_akun_debet = $('#code').val();
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    html: '<div class="form-group text-left"><label>Keterangan</label><textarea placeholder="Keterangan" name="keterangan" id="keterangan" class="form-control"></textarea></div>',
                    input: 'password',
                    inputAttributes: {
                        name: 'password',
                        placeholder: 'Password',
                        required: 'required',
                    },
                    validationMessage:'Password required',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var password = result.value;
                        var formData = new FormData();
                        var token = "{{ csrf_token() }}";
                        formData.append('_token', token);
                        formData.append('id', id);
                        formData.append('status', status);
                        formData.append('bukti_pembayaran', files[0]);
                        formData.append('password', password);
                        formData.append('id_akun_debet', id_akun_debet);
                        formData.append('old_status', old_status);
                        formData.append('tgl_transaksi', tgl_transaksi);
                        formData.append('keterangan', keterangan);
                        // getting selected checkboxes kode ambil(s)
                        /*var ids_array = table
                                        .rows({ selected: true })
                                        .data()
                                        .pluck('kode_ambil')
                                        .toArray();
                        if (ids_array.length != 0)
                        {
                            // append ids array into form
                            formData.append('kode_ambil_ids', JSON.stringify(ids_array));
                        }
                        else
                        {
                            formData.append('kode_ambil_ids', '['+id+']');
                        }*/
                        formData.append('kode_ambil_ids', '['+id+']');
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
                    });
                    }
                });
            });
        @endif

        // code array
        var bankAccountArray = [];

        // get bank account number from php
        @foreach($bankAccounts as $key => $bankAccount)
            bankAccountArray[{{ $loop->index }}]={ id : {{ $bankAccount->id }}, code: '{{ $bankAccount->CODE }}', name: '{{ $bankAccount->NAMA_TRANSAKSI }}' };
        @endforeach
        
        // trigger to get kas or bank select option
        $(document).on('change', '#jenisAkun', function ()
        {
            // remove all option in code
            $('#code').empty();

            // get jenis akun
            var jenisAkun = $('#jenisAkun').val();

            if(jenisAkun == 2)
            {
                // loop through code bank
                $.each(bankAccountArray, function(key, bankAccount)
                {
                    // set dafault to 102.18.000
                    if(bankAccount.id == 22)
                    {
                        var selected = 'selected';
                    }
                    else
                    {
                        var selected = '';
                    }

                    // insert new option
                    $('#code').append('<option value="'+bankAccount.id+'"'+ selected +'>'+bankAccount.code+ ' ' + bankAccount.name + '</option>');
                });
            }
            else if(jenisAkun == 1)
            {
                // insert new option
                $('#code').append('<option value="4" >101.01.102 KAS SIMPAN PINJAM</option>');
            }

            $('#code').trigger( "change" );
            
            $(".select2").select2({
                width: '100%',
            });
        });
    </script>
@endsection
