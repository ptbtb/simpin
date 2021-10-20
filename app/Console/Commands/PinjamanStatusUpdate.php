<?php

namespace App\Console\Commands;

Use App\Http\Controllers\PinjamanStatusController;
use Illuminate\Console\Command;

class PinjamanStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:pinjaman';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Status Pinjaman';

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
        PinjamanStatusController::runup();
    }
}
