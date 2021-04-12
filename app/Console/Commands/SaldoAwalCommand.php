<?php

namespace App\Console\Commands;

use App\Managers\SimpananManager;
use Illuminate\Console\Command;

class SaldoAwalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saldoawal:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate saldo awal anggota di t_simpan';

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
        SimpananManager::generateMutasiSimpananAnggota();
    }
}
