<?php

namespace App\Http\Controllers;

use App\Exports\BudgetExport;
use App\Models\Budget;
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
        $data['title'] = 'List Budget';
        return view('budget.index', $data);
    }

    public function indexAjax(Request $request)
    {
        $budgets = Budget::orderBy('date', 'desc');

        return DataTables::eloquent($budgets)
                        ->make();
    }

    public function create()
    {
        $data['title'] = 'Add Budget';
        return view('budget.create', $data);
    }

    public function store(Request $request)
    {
        try
        {
            DB::transaction(function () use ($request)
            {
                $budget = new Budget();
                $budget->name = $request->name;
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
        $budget = Budget::find($id);
        if (is_null($budget))
        {
            return redirect()->back()->withErrors('Budget not found');
        }

        $data['title'] = 'Edit Budget';
        $data['budget'] = $budget;
        return view('budget.edit', $data);
    }

    public function update($id, Request $request)
    {
        try
        {
            $budget = Budget::find($id);
            if (is_null($budget))
            {
                return redirect()->back()->withErrors('Budget not found');
            }

            DB::transaction(function () use ($request, $budget)
            {
                $budget->name = $request->name;
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
        $budgets = Budget::orderBy('date', 'desc')
                        ->get();

        $data['budgets'] = $budgets;

        $filename = 'export-budget.xlsx';
        return Excel::download(new BudgetExport($data), $filename);
    }
}
