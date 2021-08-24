<table class="table table-striped">
    <thead>
        <tr>
            <th>No Invoice</th>
            <th>Type</th>
            <th>Kode Anggota</th>
            <th>Nama Anggota</th>
            <th>Unit Kerja</th>
            <th>Besar Tagihan</th>
            <th>Keterangan</th>
            <th>Tanggal Invoice</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoices as $invoice)
            <tr>
                <td>{{ $invoice->invoice_number }}</td>
                <td>{{ $invoice->invoiceType->name }}</td>
                <td>{{ $invoice->anggota->kode_anggota }}</td>
                <td>{{ $invoice->anggota->nama_anggota }}</td>
                <td>{{ $invoice->anggota->company->nama }}</td>
                <td>{{ $invoice->final_amount }}</td>
                <td>{{ $invoice->description }}</td>
                <td>{{ $invoice->view_date }}</td>
                <td>{{ $invoice->view_due_date }}</td>
                <td>{{ $invoice->invoiceStatus->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
