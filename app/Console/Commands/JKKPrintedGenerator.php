<?php

namespace App\Console\Commands;

use App\Models\JkkPrinted;
use App\Models\Penarikan;
use App\Models\Pengajuan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class JKKPrintedGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jkkprinted:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate JKK Printed';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // jkk printed for loan applications
        $loanApplications = Pengajuan::whereNotNull('no_jkk')
                                    ->where('status_jkk', 1)
                                    ->get();

        foreach ($loanApplications as $loanApplication)
        {
            // find and check jkk, if already exist, skip
            $jkkPrinted = JkkPrinted::where('jkk_number', $loanApplication->no_jkk)
                                    ->where('jkk_printed_type_id', JKK_PRINTED_TYPE_PENGAJUAN_PINJAMAN)
                                    ->first();

            if ($jkkPrinted)
            {
                continue;
            }

            // create jkk printed if not exist
            $jkkPrinted = new JkkPrinted();
            $jkkPrinted->jkk_number = $loanApplication->no_jkk;
            $jkkPrinted->jkk_printed_type_id = JKK_PRINTED_TYPE_PENGAJUAN_PINJAMAN;
            $jkkPrinted->printed_at = $loanApplication->tgl_acc;
            $jkkPrinted->printed_by = $loanApplication->approved_by?$loanApplication->approved_by:Auth::user()->id;
            $jkkPrinted->save();
        }

        // jkkprinted for withdrawal
        $withdrawals = Penarikan::whereNotNull('no_jkk')
                                ->where('status_jkk', 1)
                                ->get();

        foreach ($withdrawals as $withdrawal)
        {
            // find and check jkk, if already exist, skip
            $jkkPrinted = JkkPrinted::where('jkk_number', $withdrawal->no_jkk)
                                    ->where('jkk_printed_type_id', JKK_PRINTED_TYPE_PENARIKAN_SIMPANAN)
                                    ->first();

            if ($jkkPrinted)
            {
                continue;
            }

            // create jkk printed if not exist
            $jkkPrinted = new JkkPrinted();
            $jkkPrinted->jkk_number = $withdrawal->no_jkk;
            $jkkPrinted->jkk_printed_type_id = JKK_PRINTED_TYPE_PENARIKAN_SIMPANAN;
            $jkkPrinted->printed_at = $withdrawal->tgl_acc;
            $jkkPrinted->printed_by = $withdrawal->approved_by?$withdrawal->approved_by:Auth::user()->id;
            $jkkPrinted->save();
        }
    }
}
