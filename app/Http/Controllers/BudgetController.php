<?php

namespace App\Http\Controllers;

use App\Exports\BudgetExport;
use App\Imports\BudgetImport;
use App\Models\Budget;
use App\Models\Code;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class BudgetController extends Controller
{
    public function index()
    {
		$this->authorize('view budget', Auth::user());
        $data['title'] = 'List Budget';
        return view('budget.index', $data);
    }

    public function indexAjax(Request $request)
    {
		$this->authorize('view budget', Auth::user());
        $budgets = Budget::orderBy('date', 'desc');

        return DataTables::eloquent($budgets)
                        ->make();
    }

    public function create()
    {
		$this->authorize('add budget', Auth::user());
        $data['title'] = 'Add Budget';
        return view('budget.create', $data);
    }

    public function store(Request $request)
    {
		$this->authorize('add budget', Auth::user());
        try
        {
            $code = Code::where('CODE', 'like', '%' . $request->name . '%')
                        ->first();
            if (is_null($code))
            {
                return redirect()->back()->withError('Code not found');
            }

            DB::transaction(function () use ($request, $code)
            {
                $budget = new Budget();
                $budget->code = $code->CODE;
                $budget->name = $code->NAMA_TRANSAKSI;
                $budget->date = Carbon::createFromFormat('m-Y', $request->date);
                $budget->description = $request->description;
                $budget->amount = $request->amount;
                $budget->created_by = Auth::user()->id;
                $budget->save();
            });

            return redirect()->route('budget.list')->withSuccess('Berhasil menyimpan data');
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }

    public function edit($id)
    {
		$this->authorize('edit budget', Auth::user());
        $budget = Budget::with('code')
                        ->find($id);
        if (is_null($budget))
        {
            return redirect()->back()->withErrors('Budget not found');
        };

        $data['title'] = 'Edit Budget';
        $data['budget'] = $budget;
        return view('budget.edit', $data);
    }

    public function update($id, Request $request)
    {
		$this->authorize('edit budget', Auth::user());
        try
        {
            $budget = Budget::find($id);
            if (is_null($budget))
            {
                return redirect()->back()->withErrors('Budget not found');
            }

            $code = Code::where('CODE', 'like', '%' . $request->name . '%')
                        ->first();
            if (is_null($code))
            {
                return redirect()->back()->withError('Code not found');
            }

            DB::transaction(function () use ($request, $budget, $code)
            {
                $budget->code = $code->CODE;
                $budget->name = $code->NAMA_TRANSAKSI;
                $budget->date = Carbon::createFromFormat('m-Y', $request->date);
                $budget->description = $request->description;
                $budget->amount = $request->amount;
                $budget->created_by = Auth::user()->id;
                $budget->save();
            });

            return redirect()->route('budget.list')->withSuccess('Berhasil menyimpan data');
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }

    public function excel()
    {
		$this->authorize('export budget', Auth::user());
        $budgets = Budget::orderBy('date', 'desc')
                        ->get();

        $data['budgets'] = $budgets;

        $filename = 'export-budget.xlsx';
        return Excel::download(new BudgetExport($data), $filename);
    }

    public function import()
    {
		$this->authorize('import budget', Auth::user());
        try
        {
            $data['title'] = 'Import Budget';
            return view('budget.import', $data);
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }

    public function importStore(Request $request)
    {
		$this->authorize('import budget', Auth::user());
        try
        {
            Excel::import(new BudgetImport, $request->file);
            return redirect()->route('budget.list')->withSuccess('Berhasil import data');
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }
}

