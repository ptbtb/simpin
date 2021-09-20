<?php

namespace App\Http\Controllers;

use App\Models\Code;
use Illuminate\Http\Request;

class CodeController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->search;
        if ($search == '')
        {
            $codes = Code::orderby('NAMA_TRANSAKSI', 'asc')
                            ->limit(5)
                            ->get();
        }
        else
        {
            $codes = Code::orderby('NAMA_TRANSAKSI', 'asc')
                        ->orWhere('CODE', 'like', '%' . $search . '%')
                        ->orWhere('NAMA_TRANSAKSI', 'like', '%' . $search . '%')
                        ->limit(5)
                        ->get();
        }
        $response = $codes->map(function ($code)
        {
            return [
                'id' => $code->CODE,
                'text' => $code->NAMA_TRANSAKSI
            ];
        });

        return response()->json($response, 200);
    }

    public function searchId($id)
    {
        return Code::where('CODE', 'like', '%' . $id . '%')
                    ->first();
    }
}
