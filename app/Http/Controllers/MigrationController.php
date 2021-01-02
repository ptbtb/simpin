<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class MigrationController extends Controller
{
    public function index()
    {
        $user_id = 9807;
        $user = User::find($user_id);
        $user->givePermissionTo('approve pengajuan pinjaman spv');
    }
}
