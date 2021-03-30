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
                        <td>{{ $pengajuan->view_credit_bank }}</td>
                    </tr>
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