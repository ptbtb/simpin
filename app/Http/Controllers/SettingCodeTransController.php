<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\CodeCategory;
use App\Models\CodeType;
use App\Models\NormalBalance;
use App\Models\Code;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Exports\CoaExport;
use App\Imports\CoaImport;
use Excel;

use Auth;

class SettingCodeTransController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view kode transaksi', Auth::user());
        $codetrans = Code::all();
        $data['codetrans'] = $codetrans;
        return view('setting.codetrans.index',  $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add kode transaksi', Auth::user());

        $codeTypes = CodeType::get();
        $codeCategories = CodeCategory::get();
        $normalBalances = NormalBalance::get();
        $parents = Code::where('is_parent', 1)->get();

        $data['title'] = "Tambah Code Transaksi";
        $data['codeTypes'] = $codeTypes;
        $data['codeCategories'] = $codeCategories;
        $data['parents'] = $parents;
        $data['normalBalances'] = $normalBalances;

        return view('setting.codetrans.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('add kode transaksi', Auth::user());
        try {
            // get auth user
            $user = Auth::user();

            // save into kode transaksi
            $code = new Code();
            $code->code_type_id = $request->code_type;
            $code->normal_balance_id = $request->normal_balance;
            $code->code_category_id = $request->code_category;
            $code->induk_id = $request->induk;
            $code->CODE = $request->code;
            $code->is_parent = $request->kode_summary;
            $code->NAMA_TRANSAKSI = $request->nama_transaksi;
            $code->u_entry = $user->name;
            $code->save();

            return redirect()->route('kode-transaksi-list')->withSuccess('Berhasil menambah kode transaksi');
        } catch (\Throwable $th) {
            \Log::error($th);
            return redirect()->back()->withError('Gagal menyimpan data');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('edit kode transaksi', Auth::user());
        $codeTypes = CodeType::pluck('name', 'id')->all();
        $codeCategories = CodeCategory::pluck('name', 'id')->all();
        $normalBalances = NormalBalance::pluck('name', 'id')->all();
        $parents = Code::where('is_parent', 1)->pluck('NAMA_TRANSAKSI', 'id')->all();
        $codes = Code::find($id);

        $data['title'] = "Edit Code Transaksi";
        $data['codeTypes'] = $codeTypes;
        $data['codeCategories'] = $codeCategories;
        $data['normalBalances'] = $normalBalances;
        $data['parents'] = $parents;
        $data['codes'] = $codes;
        return view('setting.codetrans.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('edit kode transaksi', Auth::user());
        try {
            // get auth user
            $user = Auth::user();
            // save into kode transaksi
            $code = Code::find($id);
            $code->code_type_id = $request->code_type;
            $code->normal_balance_id = $request->normal_balance;
            $code->code_category_id = $request->code_category;
            $code->induk_id = $request->induk;
            $code->CODE = $request->code;
            $code->is_parent = $request->kode_summary;
            $code->NAMA_TRANSAKSI = $request->nama_transaksi;
            $code->u_entry = $user->name;
            if ($request->active){
                $code->active = $request->active;
            } else {
                $code->active = 0;
            }
            $code->save();

            return redirect()->route('kode-transaksi-list')->withSuccess('Berhasil Merubah kode transaksi');
        } catch (\Throwable $th) {
            \Log::error($th);
            return redirect()->back()->withError('Gagal menyimpan data');
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete kode transaksi', Auth::user());
        try {
            $code = Code::find($id);
            $code->delete();
            return redirect()->back()->withSuccess('Berhasil Dihapus');
        } catch (\Throwable $e) {
            $message = class_basename($e) . ' in ' . basename($e->getFile()) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);

            return redirect()->back()->withError('Something wrong');
        }


    }
    public function createExcel()
    {
        try {
            $codetrans = Code::all();
            $data['codetrans'] = $codetrans;
            $filename = 'export_coa_excel_' . Carbon::now()->format('d M Y') . '.xlsx';
            return Excel::download(new CoaExport($data), $filename);
        } catch (Exception $e)
        {
            $message = class_basename( $e ) . ' in ' . basename( $e->getFile() ) . ' line ' . $e->getLine() . ': ' . $e->getMessage();
            Log::error($message);
            abort(500);
        }
    }
    public function importExcel()
    {
        $this->authorize('add kode transaksi', Auth::user());
        $data['title'] = 'Import COA';
        return view('setting.codetrans.import', $data);
    }

    public function storeImportExcel(Request $request)
    {
        $this->authorize('add kode transaksi', Auth::user());
        try {
            DB::transaction(function () use ($request) {
                Excel::import(new CoaImport, $request->file);
            });
            return redirect()->back()->withSuccess('Import data berhasil');
        } catch (\Throwable $e) {
            \Log::error($e);
            return redirect()->back()->withError('Gagal import data');
        }

    }

}
