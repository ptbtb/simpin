<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Models\Bank;
use Auth;

class BankController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('view Bank', Auth::user());
        $data['list'] = Bank::all();
        $data['request'] = $request;
        $data['title'] = 'List Bank';
        return view('bank.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('add Bank', Auth::user());
        $data['title'] = 'add Bank';
        return view('bank.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
        $this->authorize('add Bank', Auth::user());
         $bank = new Bank();
         $bank->nama = $request->nama;
         $bank->kode = $request->kode;
         $bank->save();
         return redirect()->route('bank.list')->withSuccess('Data Tersimpan');
        }catch (\Exception $e)
        {
            $message = $e->getMessage();
            if (isset($e->errorInfo[2]))
            {
                $message = $e->errorInfo[2];
            }
            return redirect()->back()->withError($message);
        }
         

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('edit Bank', Auth::user());
        $data['title'] = 'Edit Bank';
        $data['data']=Bank::findOrFail($id);
        return view('bank.edit', $data);    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            $this->authorize('edit Bank', Auth::user());
         $bank = Bank::findOrFail($id);
         $bank->nama = $request->nama;
         $bank->kode = $request->kode;
         $bank->save();
         return redirect()->route('bank.list')->withSuccess('Data Tersimpan');

        }catch (\Exception $e)
        {
            $message = $e->getMessage();
            if (isset($e->errorInfo[2]))
            {
                $message = $e->errorInfo[2];
            }
            return redirect()->back()->withError($message);
        }    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
        $this->authorize('delete Bank', Auth::user());
         $bank = Bank::find($id);
         $bank->delete();
         return redirect()->route('bank.list')->withSuccess('Data Terhapus');

        }catch (\Exception $e)
        {
            $message = $e->getMessage();
            if (isset($e->errorInfo[2]))
            {
                $message = $e->errorInfo[2];
            }
            return redirect()->back()->withError($message);
        }    }
        
}
