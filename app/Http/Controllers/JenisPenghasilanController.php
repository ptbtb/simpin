<?php

namespace App\Http\Controllers;

use App\Models\CompanyGroup;
use App\Models\JenisPenghasilan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JenisPenghasilanController extends Controller
{
    public function create(Request $request)
    {
        $companyGroups = CompanyGroup::pluck('name','id');

        $data['title'] = 'create jenis penghasilan';
        $data['companyGroups'] = $companyGroups;
        $data['request'] = $request;
        return view('jenis_penghasilan.create', $data);
    }

    public function store(Request $request)
    {
        try
        {
            foreach ($request->form_name as $key => $name)
            {
                $jenisPenghasilan = new JenisPenghasilan();
                $jenisPenghasilan->company_group_id = $request->group_id;
                $jenisPenghasilan->name = $name;
                $jenisPenghasilan->is_visible = 1;
                $jenisPenghasilan->sequence = $key+1;
                if ($key == 0)
                {
                    $jenisPenghasilan->is_penghasilan_tertentu = 0;
                    $jenisPenghasilan->rule_name = 'gaji_bulanan';
                }
                else
                {
                    $jenisPenghasilan->is_penghasilan_tertentu = 1;
                }
                $jenisPenghasilan->save();
            }
            return redirect()->back()->withSuccess('Berhasil menyimpan data');
        }
        catch (\Throwable $th)
        {
            dd($request);
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }
}
