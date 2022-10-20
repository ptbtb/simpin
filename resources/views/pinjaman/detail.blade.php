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
                <li class="breadcrumb-item"><a href="">Pinjaman</a></li>
                <li class="breadcrumb-item"><a href="">List Pinjaman</a></li>
                <li class="breadcrumb-item active">Detail Pinjaman</li>
            </ol>
        </div>
    </div>
@endsection

@section('plugins.Select2', true)

@section('css')
    <link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
    <style>
        .btn-sm {
            font-size: .8rem;
        }

        .box-custom {
            border: 1px solid black;
            border-radius: 0;
        }

        #pelunasanDipercepat th,
        #pelunasanDipercepat td {
            padding-left: .4rem !important;
            padding-right: .4rem !important;
        }

    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 style="font-weight: 600">{{ $title }}</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <tr>
                        <td>Nama</td>
                        <td>:</td>
                        <td>
                            @if ($pinjaman->anggota)
                                {{ $pinjaman->anggota->nama_anggota }}
                            @else
                                -
                            @endif
                        </td>
                        <td>Jenis Pinjaman</td>
                        <td>:</td>
                        <td>{{ $jenisPinjaman->nama_pinjaman }}</td>
                        <td>Besar Pinjaman</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($pinjaman->besar_pinjam, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Tanggal Peminjaman</td>
                        <td>:</td>
                        <td>
                            @if ($pinjaman->tgl_transaksi)
                                {{ \Carbon\Carbon::createFromFormat('Y-m-d', $pinjaman->tgl_transaksi)->format('d M Y') }}
                            @endif
                        </td>
                        <td>Lama Angsuran</td>
                        <td>:</td>
                        <td>{{ $pinjaman->lama_angsuran }}</td>
                        <td>Besar Angsuran</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($pinjaman->besar_angsuran, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Jatuh Tempo</td>
                        <td>:</td>
                        <td>{{ $pinjaman->tgl_tempo->format('d M Y') }}</td>
                        <td>Sisa Angsuran</td>
                        <td>:</td>
                        <td>{{ $pinjaman->sisa_angsuran }}</td>
                        <td>Sisa Pinjaman</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Discount</td>
                        <td>:</td>
                        <td>{{ number_format($pinjaman->total_discount, 0, ',', '.') }}</td>
                        <td>Administrasi</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($pinjaman->biaya_administrasi, 0, ',', '.') }}</td>
                        <td>Provisi</td>
                        <td>:</td>
                        <td>Rp. {{ number_format($pinjaman->biaya_provisi, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>:</td>
                        <td class="font-weight-bold" colspan="7">{{ ucwords($pinjaman->statusPinjaman->name) }}</td>
                    </tr>
                </table>
            </div>

            <div class="mt-3 p-2 box-custom">
                <div class="d-flex">
                    <h6 style="font-weight: 600">Angsuran</h6>
                    @can('percepat pelunasan pinjaman')
                        @if ($pinjaman->canPercepatPelunasan())
                            <a class="btn btn-sm btn-info ml-auto mb-2 btn-pelunasanDipercepat text-white"><i
                                    class="fas fa-handshake"></i> Pelunasan Dipercepat</a>
                        @endif
                    @endcan
                    @can('restruktur pinjaman')
                        <a style="cursor: pointer" class="btn btn-sm btn-danger ml-2 mb-2 btn-restruktur text-white" data-toggle="modal" data-target="#restrukturModal"><i class="fas fa-cash-register"></i> Restruktur
                            Pinjaman</a>
                    @endcan
                    @can('bayar angsuran pinjaman')
                        <a class="btn btn-sm btn-success ml-2 mb-2 btn-bayarAngsuran text-white"><i class="fas fa-plus"></i>
                            Bayar Angsuran</a>
                    @endcan
                    @can('bayar angsuran pinjaman')
                        <a class="btn btn-sm btn-warning ml-2 mb-2"
                            href="{{ route('pinjaman-detail-excel', [$pinjaman->kode_pinjam]) }}"><i
                                class="fas fa-download"></i> Download Excel</a>
                    @endcan
                    @if (0)
                        @can('set diskon angsuran')
                            <a class="btn btn-sm btn-primary ml-2 mb-2 btn-diskon text-white" data-toggle="modal"
                                data-target="#discountModal"><i class="fas fa-plus"></i> Discount</a>
                        @endcan
                    @endif
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Angsuran Ke</th>
                                <th>Angsuran Pokok</th>
                                <th>Jasa</th>
                                <th>Total Angsuran</th>
                                <th>Periode Pembayaran</th>
                                <th>Besar Pembayaran</th>
                                <th>Dibayar Pada Tanggal</th>
                                <th>Status</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($listAngsuranLunas->count())
                                @foreach ($listAngsuranLunas as $angsuran)
                                    <tr>
                                        <td>{{ $angsuran->angsuran_ke }}</td>
                                        <td>Rp. {{ number_format($angsuran->besar_angsuran, 0, ',', '.') }}</td>
                                        <td>Rp. {{ number_format($angsuran->jasa, 0, ',', '.') }}</td>
                                        <td>Rp. {{ number_format($angsuran->total_angsuran, 0, ',', '.') }}</td>
                                        <td>
                                            @if ($angsuran->jatuh_tempo)
                                                {{ $angsuran->jatuh_tempo->format('m-Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>Rp. {{ number_format($angsuran->total_pembayaran, 0, ',', '.') }}</td>
                                        <td>{{ $angsuran->tgl_transaksi ? \Carbon\Carbon::createFromFormat('Y-m-d', $angsuran->tgl_transaksi)->format('d M Y'): '-' }}
                                        </td>
                                        <td>{{ $angsuran->statusAngsuran->name }}</td>
                                        <td>
                                            @if ($angsuran->isLunas())
                                                <a style="cursor: pointer" class="btn btn-sm btn-info text-white mt-1"
                                                    data-action="jurnal" data-id="{{ $angsuran->kode_angsur }}"><i
                                                        class="fa fa-eye"></i> Jurnal</a>
                                                <a style="cursor: pointer" class="btn btn-sm btn-warning text-white mt-1"
                                                    data-action="edit" data-id="{{ $angsuran->kode_angsur }}"
                                                    data-tgl-transaksi="{{ $angsuran->tgl_transaksi }}"
                                                    data-pembayaran="{{ $angsuran->besar_pembayaran }}"
                                                    data-angsuran="{{ $angsuran->besar_angsuran }}"
                                                    data-jasa="{{ $angsuran->jasa }}"><i class="fa fa-edit"></i>
                                                    Edit</a>
                                            @endif
                                            @can('approve pengajuan pinjaman')
                                                @if ($angsuran->menungguApproval())
                                                    <a data-id="{{ $angsuran->kode_angsur }}"
                                                        data-status="{{ STATUS_ANGSURAN_DITERIMA }}"
                                                        class="text-white btn mt-1 btn-sm btn-success btn-approval"><i
                                                            class="fas fa-check"></i> Terima</a>
                                                    <a data-id="{{ $angsuran->kode_angsur }}"
                                                        data-status="{{ STATUS_ANGSURAN_DITOLAK }}"
                                                        class="text-white btn mt-1 btn-sm btn-danger btn-approval"><i
                                                            class="fas fa-times"></i> Tolak</a>
                                                @endif
                                            @endcan
                                            <a style="cursor: pointer" class="btn btn-sm btn-primary mt-1"
                                                data-action="info" data-id="{{ $angsuran->kode_angsur }}"><i
                                                    class="fa fa-info"></i> Info</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <td colspan="9" class="text-center">Belum ada tagihan yang di bayar</td>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @if ($pinjaman)
        @can('bayar angsuran pinjaman')
            <div id="my-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <form action="{{ route('pinjaman-bayar-angsuran', ['id' => $pinjaman->kode_pinjam]) }}" method="POST">
                    @csrf
                    <div class="modal-dialog modal-md" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>Pembayaran Angsuran</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body row">
                                <div class="form-group col-md-6" style="display: none">
                                    <label>Bulan</label>
                                    {{-- <input type="text" name="bulan" class="form-control" value="{{ ($tagihan->jatuh_tempo)? $tagihan->jatuh_tempo->format('d-m-Y'):'' }}" readonly> --}}
                                    <input type="text" name="bulan" class="form-control"
                                        value="{{ $pinjaman->tagihan_bulan->format('d-m-Y') }}">
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Total Angsuran</label>
                                    <input type="text" name="total_angsuran" class="form-control"
                                        value="Rp. {{ number_format($pinjaman->besar_angsuran, 0, ',', '.') }}" readonly>
                                </div>
                                <div class="form-group col-md-12">
                                    <label for="tgl_transaksi">Tgl Transaksi</label>
                                    <input id="tgl_transaksi" type="date" name="tgl_transaksi" class="form-control"
                                        placeholder="yyyy-mm-dd" required
                                        value="{{ Carbon\Carbon::today()->format('Y-m-d') }}">
                                </div>
                                <div class="col-12 multipleform">
                                    <div class="row pembayaranform">
                                        <div class="col-12">
                                            <hr>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Jenis Pembayaran</label>
                                            <select name="jenis_pembayaran[]" class="form-control jenisPembayaran1">
                                                <option value="0">KAS/BANK</option>
                                                @foreach ($tabungan as $value)
                                                    <option value="{{ $value->kode_trans }}">
                                                        {{ $value->jenisSimpanan->nama_simpanan }} (Rp
                                                        {{ number_format($value->besar_tabungan, 0, ',', '.') }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-6 jenisAkun1Cover">
                                            <label>Jenis Akun</label>
                                            <select name="jenis_akun[]" id="jenisAkun1" class="form-control select2 jenisAkun"
                                                required>
                                                @foreach ($listSumberDana as $sumberDana)
                                                    <option value="{{ $sumberDana->id }}">{{ $sumberDana->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12 akun1Cover">
                                            <label>Akun</label>
                                            <select name="id_akun_kredit[]" class="code1 form-control select2" required>
                                                <option value="" selected disabled>Pilih Akun</option>
                                            </select>
                                        </div>
                                        <div class="form-group col-md-12">
                                            <label>Besar Pembayaran Pokok</label>
                                            <input type="text" name="besar_pembayaran[]" class="form-control"
                                                placeholder="Besar Pembayaran">
                                        </div>
                                        {{-- <div class="form-group col-md-12 akun1Cover">
                                        <label>Akun</label>
                                        <select name="id_akun_kredit_jasa[]"  class="code1 form-control select2" required>
                                            <option value="" selected disabled>Pilih Akun</option>
                                        </select>
                                    </div> --}}
                                        <div class="form-group col-md-12">
                                            <label>Besar Pembayaran Jasa</label>
                                            <input type="text" name="besar_pembayaran_jasa[]" class="form-control"
                                                placeholder="Besar Pembayaran">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 text-right">
                                    <a class="btn btn-xs btn-danger text-white btn-delete-jenis-pembayaran"><i
                                            class="fa fa-trash"></i> Hapus Jenis Pembayaran</a>
                                    <a class="btn btn-xs btn-warning text-white btn-add-jenis-pembayaran"><i
                                            class="fa fa-plus"></i> Tambah Jenis Pembayaran</a>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endcan

        @if ($pinjaman->canPercepatPelunasan())
            <div id="my-modal1" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <form action="{{ route('pinjaman-bayar-angsuran-dipercepat', ['id' => $pinjaman->kode_pinjam]) }}"
                    method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-dialog" role="document" style="max-width: 750px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5>Pelunasan Dipercepat</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">
                                    <table class="table table-striped" id="pelunasanDipercepat">
                                        <tr>
                                            <th style="width: 15%">Bulan</th>
                                            <th>:</th>
                                            <td style="width: 20%">{{ $pinjaman->tagihan_bulan->format('M Y') }} -
                                                {{ $pinjaman->tgl_tempo->format('M Y') }}</td>
                                            <th style="width: 15%">Total Angsuran</th>
                                            <th>:</th>
                                            <td style="width: 20%" id="totalAngsuranDiscount1">Rp.
                                                {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</td>
                                            <th style="width: 15%">Denda</th>
                                            <th>:</th>
                                            <td style="width: 20%">Rp.
                                                {{ number_format($pinjaman->total_denda, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Lama Angsuran</th>
                                            <th>:</th>
                                            <td>{{ $pinjaman->LamaAngsuranBelumLunas }} Bulan</td>
                                            {{-- <th>Jasa</th>
                                            <th>:</th>
                                            <td id="jasaDiscount">Rp.
                                                {{ number_format($pinjaman->jasaPelunasanDipercepat, 0, ',', '.') }}</td> --}}
                                            <th>Pembayaran Angs Sebagian</th>
                                            <th>:</th>
                                            <td>Rp. {{ number_format($pinjaman->tunggakan, 0, ',', '.') }}</td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6"></td>
                                            <th>Total Bayar</th>
                                            <th>:</th>
                                            <td id="totalBayarDiscount1"><b>Rp.
                                                    {{ number_format($pinjaman->sisa_pinjaman, 0, ',', '.') }}</b>
                                            </td>
                                            <input type="hidden" name="total_bayar" id="totalBayarHidden1"
                                                value="{{ $pinjaman->sisa_pinjaman }}">
                                        </tr>
                                    </table>
                                </div>
                                <hr>
                                <div class="form-group mt-2">
                                    <label for="tgl_transaksi">Tgl Transaksi</label>
                                    <input id="tgl_transaksi" type="date" name="tgl_transaksi" class="form-control"
                                        placeholder="yyyy-mm-dd" required
                                        value="{{ Carbon\Carbon::today()->format('Y-m-d') }}">
                                </div>
                                {{-- <div id="viewSaldo" class="form-group" style="display: none">
                                <label for="saldo">Sisa saldo</label>
                                <input type="text" name="saldo" id="saldo" class="form-control" readonly>
                            </div> --}}

                                {{-- <div class="form-group">
                                    <label>Discount (%)</label>
                                    <input type="number" name="discount" id="discount" class="form-control"
                                        placeholder="Ex: 15 (15%)" min="0" max="100">
                                </div> --}}
                                <div class="form-group">
                                    <label>Jasa Yang Dibayarkan</label>
                                    <input type="number" name="service_fee" id="service_fee" class="form-control"
                                        placeholder="Angka" min="0">
                                </div>
                                <div class="form-group">
                                    <label>Dokumen Konfirmasi</label>
                                    <br>
                                    <input type="file" name="confirmation_document" id="confirmationDocument">
                                </div>
                                <div class="col-12 multipleform">
                                    <div class="row pembayaranform">
                                        <div class="col-12">
                                            <hr>
                                        </div>
                                        <div class="col-md-6 form-group mt-2">
                                            <label for="jenisPembayaran">Jenis Pembayaran</label>
                                            <select name="jenis_pembayaran[]" id="jenisPembayaran" class="form-control">
                                                <option value="0">Tunai</option>
                                                @foreach ($tabungan as $value)
                                                    <option value="{{ $value->kode_trans }}">
                                                        {{ $value->jenisSimpanan->nama_simpanan }} (Rp
                                                        {{ number_format($value->besar_tabungan, 0, ',', '.') }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 form-group" id="jenisAkun2Cover">
                                            <label>Jenis Akun</label>
                                            <select name="jenis_akun[]" id="jenisAkun2"
                                                class="form-control select2 jenisAkun" required>
                                                @foreach ($listSumberDana as $sumberDana)
                                                    <option value="{{ $sumberDana->id }}">{{ $sumberDana->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-12 form-group" id="akun2Cover">
                                            <label>Akun</label>
                                            <select name="id_akun_kredit[]" class="form-control code2 select2" required>
                                                <option value="" selected disabled>Pilih Akun</option>
                                            </select>
                                        </div>
                                        <div class="col-12 form-group">
                                            <label>Besar Pembayaran</label>
                                            <input type="text" name="besar_pembayaran[]" class="form-control"
                                                placeholder="Besar Pembayaran">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="">Keterangan</label>
                                    <textarea name="description" id="description" class="form-control"></textarea>
                                </div>
                                <div class="col-12 text-right">
                                    <a class="btn btn-xs btn-danger text-white btn-delete-jenis-pembayaran"><i
                                            class="fa fa-trash"></i> Hapus Jenis Pembayaran</a>
                                    <a class="btn btn-xs btn-warning text-white btn-add-jenis-pembayaran"><i
                                            class="fa fa-plus"></i> Tambah Jenis Pembayaran</a>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-success">Submit</button>
                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif

        @if (0)
            @can('set diskon angsuran')
                <!-- Modal -->
                <div class="modal fade" id="discountModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId"
                    aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form action="{{ route('pinjaman-set-discount', [$pinjaman->kode_pinjam]) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Set Discount</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <tr>
                                                <th>Angsuran Pokok</th>
                                                <th>:</th>
                                                <td>{{ number_format($pinjaman->besar_angsuran_pokok, 0, ',', '.') }}</td>
                                                <th>Jasa</th>
                                                <th>:</th>
                                                <td>{{ number_format($pinjaman->biaya_jasa, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Discount (%)</th>
                                                <th>:</th>
                                                <td>
                                                    <input type="number" name="discount" id="discount" class="form-control"
                                                        placeholder="Ex: 15 (15%)" min="0" max="100">
                                                </td>
                                                <th>Total Angsuran</th>
                                                <th>:</th>
                                                <td id="totalAngsuranDiscount">
                                                    {{ number_format($pinjaman->besar_angsuran, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        @endif
    @endif

    <div id="modal-edit" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <form action="{{ route('pinjaman-edit-angsuran') }}" method="POST">
            @csrf
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit Angsuran</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body row modal-edit-body">
                        <input type="hidden" name="kode_angsur" id="kode_angsur" class="form-control" value="">
                        <div class="form-group col-md-6">
                            <label>Bulan</label>
                            <input type="text" name="bulan" class="form-control"
                                value="{{ $pinjaman->tgl_tempo ? $pinjaman->tgl_tempo->format('d-m-Y') : '' }}" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label>Total Angsuran</label>
                            <input type="text" name="total_angsuran" id="total_angsuran" class="form-control" value=""
                                readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="tgl_transaksi">Tgl Transaksi</label>
                            <input id="tgl_transaksi" type="date" name="tgl_transaksi" class="form-control"
                                placeholder="yyyy-mm-dd" required>
                        </div>
                        <div class="form-group col-md-12">
                            <label>Besar Pembayaran</label>
                            <input type="text" name="besar_pembayaran" id="besar_pembayaran" class="form-control"
                                placeholder="Besar Pembayaran">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @can('restruktur pinjaman')
        <!-- Modal -->
        <div class="modal fade" id="restrukturModal" tabindex="-1" role="dialog" aria-labelledby="restrukturPinjaman"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('pinjaman.restruktur.store') }}" method="POST" enctype="multipart/form-data">
                       @csrf
                       <input type="hidden" name="kode_pinjam" value="{{ $pinjaman->kode_pinjam }}">
                        <div class="modal-header">
                            <h5 class="modal-title">Restruktur Pinjaman</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                              <label for="sisa_pinjaman">Sisa Pinjaman</label>
                              <input type="text" name="sisa_pinjaman" id="sisa_pinjaman" class="form-control" placeholder="Sisa Pinjaman" value="Rp. {{ number_format($pinjaman->sisa_pinjaman, 0,',', '.') }}" readonly>
                            </div>
                            <div class="form-group">
                              <label for="new_tenor">Tenor Baru</label>
                              <input type="number" name="new_tenor" id="new_tenor" class="form-control" placeholder="Tenor Baru" required>
                            </div>
                            {{-- <div class="form-group">
                              <label for="angsuran_baru">Angsuran Baru</label>
                              <input type="text" name="angsuran_baru" id="angsuran_baru" class="form-control" placeholder="Angsuran Baru" readonly>
                            </div> --}}
                            <div class="form-group">
                                <label>Dokumen Persetujuan Restruktur Pinjaman</label>
                                <br>
                                <input type="file" name="dokumen_persetujuan" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/collect.js/4.29.0/collect.min.js"></script> --}}
    <script>
        // $.fn.dataTable.ext.errMode = 'none';
        var baseURL = {!! json_encode(url('/')) !!};
        var saldo = collect(@json($tabungan))
        var listSumberDana = collect(@json($listSumberDana));
        $(document).on('click', '.btn-bayarAngsuran', function() {
            $('#my-modal').modal({
                backdrop: false
            });
            $('#my-modal').modal('show');
            $('.jenisAkun').trigger("change");
        });

        $(document).on('click', '.btn-pelunasanDipercepat', function() {
            $('#my-modal1').modal({
                backdrop: false
            });
            $('#my-modal1').modal('show');
            $('.jenisAkun').trigger("change");
        });

        $(document).on('change', '#jenisPembayaran', function() {
            var selected = this.value;
            if (selected != 0) {
                $('#jenisAkun2Cover').addClass('d-none');
                $('#akun2Cover').addClass('d-none');
                // console.log(saldo.where('kode_trans', selected).first().besar_tabungan);
                // // $('#viewSaldo').show();
                // // $('#saldo').val();
            } else {
                $('#jenisAkun2Cover').removeClass('d-none');
                $('#akun2Cover').removeClass('d-none');
            }
        })

        $(document).on('change', '.jenisPembayaran1', function() {
            var selected = this.value;
            var rootClass = $(this).parent().parent();
            if (selected != 0) {
                rootClass.find('.jenisAkun1Cover').addClass('d-none');
                rootClass.find('.akun1Cover').addClass('d-none');
                rootClass.find('.jenisPembayaran1').parent().addClass('col-md-12');
                // console.log(saldo.where('kode_trans', selected).first().besar_tabungan);
                // // $('#viewSaldo').show();
                // // $('#saldo').val();
            } else {
                rootClass.find('.jenisAkun1Cover').removeClass('d-none');
                rootClass.find('.akun1Cover').removeClass('d-none');
                rootClass.find('.jenisPembayaran1').parent().removeClass('col-md-12');
            }
        })

        $(".select2").select2({
            width: '100%',
        });

        // code array
        var bankAccountArray = [];

        // get bank account number from php
        @foreach ($bankAccounts as $key => $bankAccount)
            bankAccountArray[{{ $loop->index }}] = {
                id: {{ $bankAccount->id }},
                code: '{{ $bankAccount->CODE }}',
                name: '{{ $bankAccount->NAMA_TRANSAKSI }}'
            };
        @endforeach

        // trigger to get kas or bank select option
        $(document).on('change', '.jenisAkun', function() {
            var rootClass = $(this).parent().parent();
            // remove all option in code
            rootClass.find('.code1').empty();
            rootClass.find('.code2').empty();

            // get jenis akun
            var jenisAkun = $(this).val();
            selectedSumberDana = listSumberDana.where('id', parseInt(jenisAkun)).first();
            currentCodes = collect(selectedSumberDana.codes);

            var pattern = "";
            currentCodes.each(function(code) {
                if (code.id == 22) {
                    pattern = pattern + '<option value="' + code.id + '" selected>' + code.CODE + ' ' + code
                        .NAMA_TRANSAKSI + '</option>';
                } else {
                    pattern = pattern + '<option value="' + code.id + '">' + code.CODE + ' ' + code
                        .NAMA_TRANSAKSI + '</option>';
                }
            });
            $('.code1').html(pattern);
            $('.code2').html(pattern);
            /*
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
                    rootClass.find('.code1').append('<option value="'+bankAccount.id+'"'+ selected +'>'+bankAccount.code+ ' ' + bankAccount.name + '</option>');
                    rootClass.find('.code2').append('<option value="'+bankAccount.id+'"'+ selected +'>'+bankAccount.code+ ' ' + bankAccount.name + '</option>');
                });
            }
            else if(jenisAkun == 1)
            {
                // insert new option
                rootClass.find('.code1').append('<option value="4" >101.01.102 KAS SIMPAN PINJAM</option>');
                rootClass.find('.code2').append('<option value="4" >101.01.102 KAS SIMPAN PINJAM</option>');
            }
            */
            rootClass.find('.code1').trigger("change");
            rootClass.find('.code2').trigger("change");
        });

        // action button on click
        $(document).on('click', 'a', function() {
            var dataId = $(this).data('id');
            var action = $(this).data('action');
            var listAngsuran = collect(@json($listAngsuran));
            var listAngsuranLunas = collect(@json($listAngsuranLunas));

            // show jurnal
            if (action == 'jurnal') {
                // var dataId = $(this).data('id');
                $.ajax({
                    url: baseURL + '/pinjaman/angsuran/jurnal/' + dataId,
                    success: function(data, status, xhr) {
                        var htmlText = data;
                        Swal.fire({
                            title: 'Info',
                            html: htmlText,
                            showCancelButton: false,
                            confirmButtonText: "Tutup",
                            confirmButtonColor: "#00ff00",
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: 'Error',
                            html: 'Terjadi Kesalahan',
                            icon: "error",
                            showCancelButton: false,
                            confirmButtonText: "Tutup",
                            confirmButtonColor: "#00ff00",
                        });
                    }
                });
                // var angsuran = listAngsuran.where('kode_angsur', dataId).first();
                // var jurnals = collect(angsuran.jurnals);
                // var htmlText = '<table class="table" style="font-size: 14px" >' +
                //                     '<thead class="thead-dark">' +
                //                         '<tr>' +
                //                             '<th>Akun Debet</th>' +
                //                             '<th>Debet</th>' +
                //                             '<th>Akun Kredit</th>' +
                //                             '<th>Kredit</th>' +
                //                             '</tr>' +
                //                     '</thead>' +
                //                     '<tbody>';
                // jurnals.each(function (jurnal)
                // {
                //     var body = '<tr>' +
                //                     '<td>' + jurnal['akun_debet'] + '</td>' +
                //                     '<td> Rp ' + new Intl.NumberFormat(['ban', 'id']).format(jurnal['debet']) + '</td>' +
                //                     '<td>' + jurnal['akun_kredit'] + '</td>' +
                //                     '<td> Rp ' + new Intl.NumberFormat(['ban', 'id']).format(jurnal['kredit']) + '</td>' +
                //                 '</tr>';
                //
                //     htmlText = htmlText + body;
                // });
                //
                // htmlText = htmlText + '</tbody></table>';
                //
                // Swal.fire({
                //     title: 'Jurnal',
                //     html: htmlText,
                //     showCancelButton: false,
                //     confirmButtonText: "Ok",
                //     confirmButtonColor: "#00a65a",
                // });
            } else if (action == 'info') {
                var angsuran = listAngsuranLunas.where('kode_angsur', dataId).first();
                var htmlText = '<div class="container-fluid">' +
                    '<div class="row">' +
                    '<div class="col-md-6 mx-0 my-2">Created At <br> <b>' + angsuran['created_at_view'] +
                    '</b></div>' +
                    '<div class="col-md-6 mx-0 my-2">Created By <br> <b>' + angsuran['created_by_view'] +
                    '</b></div>' +
                    '<div class="col-md-6 mx-0 my-2">Updated At <br> <b>' + angsuran['updated_at_view'] +
                    '</b></div>' +
                    '<div class="col-md-6 mx-0 my-2">Created By <br> <b>' + angsuran['updated_by_view'] +
                    '</b></div>' +
                    '</div>' +
                    '</div>';

                Swal.fire({
                    title: 'Info',
                    html: htmlText,
                    showCancelButton: false,
                    confirmButtonText: "Ok",
                    confirmButtonColor: "#00a65a",
                });
            } else if (action == 'edit') {
                var dataTglTransaksi = $(this).data('tgl-transaksi');
                var dataBesarPembayaran = $(this).data('pembayaran');
                var dataAngsuran = $(this).data('angsuran');
                var dataJasa = $(this).data('jasa');

                $('#modal-edit').modal({
                    backdrop: false
                });
                $('#modal-edit').modal('show');

                $(".modal-edit-body #kode_angsur").val(dataId);
                $(".modal-edit-body #tgl_transaksi").val(dataTglTransaksi);
                $(".modal-edit-body #besar_pembayaran").val(dataBesarPembayaran);
                $(".modal-edit-body #total_angsuran").val(dataAngsuran + dataJasa);
            }
        });

        $(document).on('click', '.btn-approval', function() {
            var id = $(this).data('id');
            var status = $(this).data('status');
            var url = '{{ route('pinjaman-angsuran-update-status') }}';

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                input: 'password',
                inputAttributes: {
                    name: 'password',
                    placeholder: 'Password',
                    required: 'required',
                    validationMessage: 'Password required',
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
                        error: function(error) {
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

        function toRupiah(number) {
            var stringNumber = number.toString();
            var length = stringNumber.length;
            var temp = length;
            var res = "Rp ";
            for (let i = 0; i < length; i++) {
                res = res + stringNumber.charAt(i);
                temp--;
                if (temp % 3 == 0 && temp > 0) {
                    res = res + ".";
                }
            }
            return res;
        }

        /*$('#discount').on('keyup', function ()
        {
            var discount = $(this).val();
            var jasa = {{ $pinjaman->biaya_jasa }};
            var angsuranPokok = {{ $pinjaman->besar_angsuran_pokok }};
            var totalDiscount = discount/100*jasa;
            var totalAngsuran = angsuranPokok + jasa - totalDiscount;
            $('#totalAngsuranDiscount').html(toRupiah(totalAngsuran));
        })*/

        $('#discount').on('keyup', function() {
            var discount = $(this).val();
            var jasaPelunasanDipercepat = 0;
            // console.log(jasaPelunasanDipercepat);
            var totalAngsuran = {{ $pinjaman->sisa_pinjaman }};
            // console.log(totalAngsuran);
            var totalDenda = {{ $pinjaman->totalDenda }};
            // console.log(totalDenda);
            var tunggakan = {{ $pinjaman->tunggakan }};
            // console.log(tunggakan);
            var totalDiscount = discount / 100 * jasaPelunasanDipercepat;
            // console.log(totalDiscount);
            var total = totalAngsuran + totalDenda + tunggakan + jasaPelunasanDipercepat - totalDiscount;
            // console.log(total);
            $('#totalAngsuranDiscount1').html(toRupiah(Math.round(totalAngsuran)));
            $('#jasaDiscount').html(toRupiah(Math.round(jasaPelunasanDipercepat - totalDiscount)));
            $('#totalBayarDiscount1').html("<b>" + toRupiah(Math.round(total)) + "<b>");
            $('#totalBayarHidden1').val(Math.round(total));
        })

        $(document).on('click', '.btn-add-jenis-pembayaran', function() {
                var pattern = '<div class="row pembayaranform">' +
                    '<div class="col-12"><hr></div><div class="form-group col-md-6">' +
                    '<label>Jenis Pembayaran</label>' +
                    '<select name="jenis_pembayaran[]" class="form-control jenisPembayaran1">' +
                    '<option value="0">KAS/BANK</option>' +
                    '@foreach ($tabungan as $value)' +
                '<option value="{{ $value->kode_trans }}">{{ $value->jenisSimpanan->nama_simpanan }} (Rp {{ number_format($value->besar_tabungan, 0, ',', '.') }})</option>' +
                '@endforeach' +
            '</select>' +
            '</div>' +
            '<div class="form-group col-md-6 jenisAkun1Cover">' +
            '<label>Jenis Akun</label>' +
            '<select name="jenis_akun[]" id="jenisAkun1" class="form-control select2 jenisAkun" required>' +
            '@foreach ($listSumberDana as $sumberDana)' +
            '<option value="{{ $sumberDana->id }}">{{ $sumberDana->name }}</option>' +
            '@endforeach' +
        '</select>' +
        '</div>' +
        '<div class="form-group col-md-12 akun1Cover">' +
        '<label>Akun</label>' +
        '<select name="id_akun_kredit[]" class="code1 form-control select2" required>' +
        '<option value="" selected disabled>Pilih Akun</option>' +
        '</select>' +
        '</div>' +
        '<div class="form-group col-md-12">' +
        '<label>Besar Pembayaran</label>' +
        '<input type="text" name="besar_pembayaran[]" class="form-control" placeholder="Besar Pembayaran">' +
        '</div>' +
        // '<div class="form-group col-md-12 akun1Cover">' +
        //     '<label>Akun</label>' +
        //     '<select name="id_akun_kredit_jasa[]" class="code1 form-control select2" required>' +
        //         '<option value="" selected disabled>Pilih Akun</option>' +
        //     '</select>' +
        // '</div>' +
        '<div class="form-group col-md-12">' +
        '<label>Besar Pembayaran Jasa</label>' +
        '<input type="text" name="besar_pembayaran_jasa[]" class="form-control" placeholder="Besar Pembayaran Jasa">' +
        '</div>' +
        '</div>';

        $('.multipleform').append(pattern);
        $('.select2').select2();

        var lastChild = $('.multipleform').find('.pembayaranform:last');
        lastChild.find('.jenisAkun').trigger("change");
        // console.log(pattern);
        });

        $(document).on('click', '.btn-delete-jenis-pembayaran', function() {
            // var countChild = $('.multipleform').find('.pembayaranform').length;
            // var lastChild = $('.multipleform').find('.pembayaranform:last');
            // if(countChild > 1)
            //     lastChild.remove();
            var countChild = $(this).parent().parent().find('.pembayaranform').length;
            var lastChild = $(this).parent().parent().find('.pembayaranform:last');
            if (countChild > 1)
                lastChild.remove();
        });
    </script>

    <script>
        $(document).on('keyup', '#new_tenor', function ()
        {
            var curentVal = $(this).val();
            var sisa_pinjaman = $('#sisa_pinjaman').val();
            sisa_pinjaman = sisa_pinjaman.replace(/[^\d]/g, "",'');
            var angsuranBaru = sisa_pinjaman/curentVal;
            if(curentVal === "")
            {
                $('#angsuran_baru').val("");
            }
            else
            {
                $('#angsuran_baru').val(toRupiah(Math.round(angsuranBaru)));
            }
        });

        function toRupiah(number) {
            var stringNumber = number.toString();
            var length = stringNumber.length;
            var temp = length;
            var res = "Rp ";
            for (let i = 0; i < length; i++) {
                res = res + stringNumber.charAt(i);
                temp--;
                if (temp % 3 == 0 && temp > 0) {
                    res = res + ".";
                }
            }
            return res;
        }
    </script>
@endsection
