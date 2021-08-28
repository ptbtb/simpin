<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function index()
    {
        $data['title'] = 'List Company';
        $data['companies'] = Company::with('companyGroup')
                                    ->get();
        return view('company.index', $data);
    }

    public function edit($id)
    {
        $company = Company::findOrFail($id);
        $groups = CompanyGroup::pluck('name', 'id');
        $data['company'] = $company;
        $data['groups'] = $groups;
        return view('company.edit', $data);
    }

    public function update(Request $request, $id)
    {
        try
        {
            $company = Company::find($id);
            if (is_null($company))
            {
                return redirect()->back()->withErrors('Company not found');
            }

            DB::transaction(function () use($company, $request)
            {
                $company->company_group_id = $request->company_group_id;
                $company->nama = $request->company_name;
                $company->save();
            });

            return redirect()->back()->withSuccess('Berhasil update data');
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }
}
