<?php

namespace App\Http\Controllers;

use App\Models\Code;
use App\Models\SumberDana;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SumberDanaController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $this->authorize('view sumber dana', $user);
        try
        {
            $listSumberDana = SumberDana::get();
            $data['title'] = 'List Sumber Dana';
            $data['listSumberDana'] = $listSumberDana;
            return view('sumber-dana.index', $data);
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }

    public function create()
    {
        $user = Auth::user();
        $this->authorize('add sumber dana', $user);
        try
        {
            $codes = Code::whereNull('sumber_dana_id')
                            ->get();
            $data['title'] = 'Tambah Sumber Dana';
            $data['codes'] = $codes;
            return view('sumber-dana.create', $data);
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $this->authorize('add sumber dana', $user);
        try
        {
            DB::transaction(function () use ($request)
            {
                $sumberDana = new SumberDana();
                $sumberDana->name = $request->name;
                $sumberDana->save();

                if($request->code && count($request->code))
                {
                    foreach ($request->code as $requestedId)
                    {
                        $code = Code::find($requestedId);
                        if($code)
                        {
                            $code->sumber_dana_id = $sumberDana->id;
                            $code->save();
                        }
                    }
                }
            });

            return redirect()->route('sumber-dana.index')->withSuccess('Data berhasil disimpan');
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
        $user = Auth::user();
        $this->authorize('edit sumber dana', $user);
        try
        {
            $sumberDana = SumberDana::with('codes')
                                    ->findOrFail($id);
            $codes = Code::whereNull('sumber_dana_id')
                            ->get();
            $data['title'] = 'Edit Sumber Dana';
            $data['codes'] = $codes;
            $data['sumberDana'] = $sumberDana;
            return view('sumber-dana.edit', $data);
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }

    public function update($id, Request $request)
    {
        $user = Auth::user();
        $this->authorize('edit sumber dana', $user);
        try
        {
            $sumberDana = SumberDana::with('codes')
                                    ->findOrFail($id);
            $savedCodes = $sumberDana->codes->pluck('id');
            $requestedCodes = collect($request->code);

            $updatedCodes = $savedCodes->intersect($requestedCodes);
            $newCodes = $requestedCodes->diff($updatedCodes);
            $deletedCodes = $savedCodes->diff($updatedCodes);
            
            DB::transaction(function () use ($newCodes, $deletedCodes, $sumberDana)
            {
                // set new code
                foreach ($newCodes as $newCode)
                {
                    $code = Code::find($newCode);
                    if($code)
                    {
                        $code->sumber_dana_id = $sumberDana->id;
                        $code->save();
                    }
                }
    
                // deleted code
                foreach ($deletedCodes as $deletedCode)
                {
                    $code = Code::find($deletedCode);
                    if($code)
                    {
                        $code->sumber_dana_id = null;
                        $code->save();
                    }
                }
            });
            
            return redirect()->route('sumber-dana.index')->withSuccess('Data berhasil disimpan');
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $this->authorize('delete sumber dana', $user);
        try
        {
            $sumberDana = SumberDana::with('codes')
                                    ->findOrFail($id);

            DB::transaction(function () use ($sumberDana)
            {
                // deleted related code
                foreach ($sumberDana->codes as $code)
                {
                    $code->sumber_dana_id = null;
                    $code->save();
                }
    
                $sumberDana->delete();
            });
            
            return redirect()->route('sumber-dana.index')->withSuccess('Data berhasil dihapus');
        }
        catch (\Throwable $th)
        {
            $message = $th->getMessage().' || '. $th->getFile().' || '. $th->getLine();
            Log::error($message);
            return redirect()->back()->withErrors($message);
        }
    }
}