<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Http\Request;

class UserExport implements FromView
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $users = User::with('anggota');
        if ($this->request->role_id)
        {
            $roleId = $this->request->role_id;
            $users = $users->whereHas('roles', function ($query) use ($roleId)
			{
				return $query->where('id', $roleId);
			});
        }

        $users = $users->get();
        return view('user.excel', [
            'users' => $users
        ]);
    }
}
