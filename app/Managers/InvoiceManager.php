<?php
namespace App\Managers;

use App\Events\Invoice\InvoiceCreated;
use App\Models\Anggota;
use App\Models\Invoice;
use App\Models\KelasSimpanan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceManager
{
    public function generateInvoiceMonthly()
    {
        try
        {
            // main code
            Anggota::has('company')
                    ->chunk(1000, function ($listAnggota)
                    {
                        foreach ($listAnggota as $anggota) {
                             // generate invoice pinjaman
                            $this->generateInvoicePinjamanUser($anggota);

                            // generate invoice simpanan
                            $this->generateInvoiceSimpananUser($anggota);
                                    }
                    });
            
            // tester code
            /*
            $a = Anggota::has('company')
                        ->has('listPinjaman')
                        ->take(10)
                        ->get();
    
            foreach ($a as $b)
            {
                // generate invoice pinjaman
                $this->generateInvoicePinjamanUser($b);

                // generate invoice simpanan
                $this->generateInvoiceSimpananUser($b);
            }*/

            echo 'Invoice Generated';
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            echo 'Error found';
        }
    }

    public function generateInvoicePinjamanUser(Anggota $anggota)
    {
        $pinjamanBelumLunas = $anggota->listPinjaman->where('id_status_pinjaman', STATUS_ANGSURAN_BELUM_LUNAS)->values();
        foreach ($pinjamanBelumLunas as $pinjaman)
        {
            $angsuran = $pinjaman->angsuranBulanIni;
            // create invoic if exist
            if ($angsuran)
            {
                DB::transaction(function () use ($angsuran, $anggota)
                {
                    $invoice = new Invoice();
                    $invoice->invoice_type_id = INVOICE_TYPE_PINJAMAN;
                    $invoice->invoice_number = $anggota->kode_anggota.$this->incrementalHash();
                    $invoice->kode_anggota = $anggota->kode_anggota;
                    $invoice->description = 'Tagihan pinjaman bulan '. Carbon::now()->format('F');
                    $invoice->amount = $angsuran->besar_angsuran;
                    $invoice->discount = 0;
                    $invoice->tax = 0;
                    $invoice->final_amount = $angsuran->besar_angsuran;
                    $invoice->date = Carbon::now();
                    $invoice->due_date = $angsuran->jatuh_tempo;
                    $invoice->paid_date = null;
                    $invoice->invoice_status_id = INVOICE_STATUS_UNPAID;
                    $invoice->version = 1;
                    $invoice->save();

                    event(new InvoiceCreated($invoice));
                });
            }
        }
    }

    public function generateInvoiceSimpananUser(Anggota $anggota)
    {
        // simpanan wajib
        $besarSimpananWajib = KelasSimpanan::where('kelas_company_id', $anggota->kelas_company_id)
                                            ->first();
        
        if ($besarSimpananWajib)
        {
            DB::transaction(function () use ($besarSimpananWajib, $anggota)
            {
                $invoice = new Invoice();
                $invoice->invoice_type_id = INVOICE_TYPE_SIMPANAN;
                $invoice->invoice_number = $anggota->kode_anggota.$this->incrementalHash();
                $invoice->kode_anggota = $anggota->kode_anggota;
                $invoice->description = 'Tagihan simpanan wajib bulan '. Carbon::now()->format('F');
                $invoice->amount = $besarSimpananWajib->simpanan;
                $invoice->discount = 0;
                $invoice->tax = 0;
                $invoice->final_amount = $besarSimpananWajib->simpanan;
                $invoice->date = Carbon::now();
                $invoice->due_date = Carbon::now()->endOfMonth();
                $invoice->paid_date = null;
                $invoice->invoice_status_id = INVOICE_STATUS_UNPAID;
                $invoice->version = 1;
                $invoice->save();

                event(new InvoiceCreated($invoice));
            });
        }
    }

    public static function incrementalHash()
    {
        $charset = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $base = strlen($charset);
        $result = '';

        $now = explode(' ', microtime())[1];
        while ($now >= $base){
            $i = $now % $base;
            $result = $charset[$i] . $result;
            $now /= $base;
        }
        return substr($result, -5);
    }
}