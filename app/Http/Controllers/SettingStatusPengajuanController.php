<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\StatusPengajuan;

use Auth;

class SettingStatusPengajuanController extends Controller
{
    public function index()
    {
        $this->authorize('view status pengajuan', Auth::user());
        $statusPengajuan = StatusPengajuan::all();
        $data['statusPengajuans'] = $statusPengajuan;
        $data['title'] = 'Status Pengajuan';
        return view('setting.statusPengajuan.index', $data);
    }

    public function create()
    {
        $this->authorize('add status pengajuan', Auth::user());

        $data['title'] = 'Create Status Pengajuan';
        return view('setting.statusPengajuan.create', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('add status pengajuan', Auth::user());
        try {
            $statusPengajuan = new StatusPengajuan();
            $statusPengajuan->name = $request->name;
            $statusPengajuan->batas_pengajuan =  filter_var($request->batas_pengajuan, FILTER_SANITIZE_NUMBER_INT);
            $statusPengajuan->save();
            // dd($request);
            return redirect()->route('status-pengajuan-list')->withSuccess('Create Status Pengajuan Sukses');
        } catch (\Exception $e) {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
                $message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
    }

    public function edit($id)
    {
        $this->authorize('edit status pengajuan', Auth::user());
        $statusPengajuan = StatusPengajuan::find($id);
        $data['statusPengajuan'] = $statusPengajuan;
        $data['title'] = 'Edit Status Pengajuan';
        return view('setting.statusPengajuan.edit', $data);
    }

    public function update($id, Request $request)
    {
        $this->authorize('edit status pengajuan', Auth::user());
        $statusPengajuan = StatusPengajuan::find($id);
        try {
            $statusPengajuan->name = $request->name;
            $statusPengajuan->batas_pengajuan =  filter_var($request->batas_pengajuan, FILTER_SANITIZE_NUMBER_INT);
            $statusPengajuan->save();
            return redirect()->route('status-pengajuan-list')->withSuccess('Update Status Pengajuan Sukses');
        } catch (\Exception $e) {
            $message = $e->getMessage();
			if (isset($e->errorInfo[2]))
			{
                $message = $e->errorInfo[2];
			}
			return redirect()->back()->withError($message);
        }
    }

    public function destroy($id)
    {
        $this->authorize('delete status pengajuan', Auth::user());

        $statusPengajuan = StatusPengajuan::findOrFail($id);
        $statusPengajuan->delete();
        return redirect()->route('status-pengajuan-list')->withSuccess('Delete Status Pengajuan Success');
    }
}
