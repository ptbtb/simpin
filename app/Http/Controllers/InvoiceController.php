<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceStatus;
use App\Models\InvoiceType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        try
        {
            $data['title'] = 'List Invoice';
            $data['invoiceStatus'] = InvoiceStatus::get()->pluck('name','id');
            $data['invoiceType'] = InvoiceType::get()->pluck('name','id');
            $data['request'] = $request;
            return view('invoice.index', $data);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
        }
    }

    public function indexAjax(Request $request)
    {
        try
        {
            $user = Auth::user();
            $invoices = Invoice::with('anggota', 'invoiceStatus', 'invoiceType');

            if ($request->invoice_status_id)
            {
                $invoices = $invoices->where('invoice_status_id', $request->invoice_status_id);
            }

            if ($request->invoice_type_id)
            {
                $invoices = $invoices->where('invoice_type_id', $request->invoice_type_id);
            }

            if($user->isAnggota())
            {
                $invoices = $invoices->where('kode_anggota', $user->anggota->kode_anggota);
            }

            $invoices = $invoices->orderBy('date', 'desc');
            return DataTables::eloquent($invoices)->make(true);
        }
        catch (\Throwable $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
        }
    }

    public function show($id)
    {
        $invoice = Invoice::findOrFail($id);
        $data['invoice'] = $invoice;
        $data['title'] = 'Detail Invoice';
        return view('invoice.detail', $data);
    }
}
