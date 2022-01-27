<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 offset-md-3" style="font-size: 15px">
            <table class="table">
                <thead class="thead-dark">
                    <tr>
                        <th>Akun Debet</th>
                        <th>Debet</th>
                        <th>Akun Kredit</th>
                        <th>Kredit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $pengajuan->kode_jenis_pinjam }}</td>
                        <td>Rp. {{ number_format($pengajuan->besar_pinjam, 2, ',', '.') }}</td>
                        <td>Bank/Kas</td>
                        <td>{{ number_format($pengajuan->pinjaman->pinjamanDitransfer,2,',','.') }}</td>
                    </tr>
                    @if ($pengajuan->pengajuanTopup->count())
                      <tr>
                          <td></td>
                          <td></td>
                          @php
                            $jenis = substr($pengajuan->pengajuanTopup[0]->pinjaman->kode_jenis_pinjam,0,3);
                            if ($jenis==106){
                              $jasatopup='701.02.014';
                            }elseif($jenis==105){
                              $jasatopup='701.02.015';
                            }

                          @endphp
                            <td>{{$jasatopup}}</td>
                            <td>{{ $pengajuan->viewJasaPelunasanDipercepat }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>{{ $pengajuan->pengajuanTopup[0]->pinjaman->kode_jenis_pinjam}}</td>
                            <td>{{ $pengajuan->viewSisaPinjaman }}</td>
                        </tr>
                    @endif
                    @if ($pengajuan->transfer_simpanan_pagu)
                      <tr>
                          <td></td>
                          <td></td>
                            <td>Simpanan Pagu</td>
                            <td>Rp {{ number_format($pengajuan->transfer_simpanan_pagu, '2', ',', '.') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td></td>
                        <td></td>
                        <td>701.02.004</td>
                        <td>{{ $pengajuan->view_provisi }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>701.02.011</td>
                        <td>{{ $pengajuan->view_biaya_admin }}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>404.04.000</td>
                        <td>{{ $pengajuan->view_asuransi }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
