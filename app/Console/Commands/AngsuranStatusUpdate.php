<?php

namespace App\Console\Commands;

Use App\Http\Controllers\PinjamanStatusController;
use Illuminate\Console\Command;

class AngsuranStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:angsuran';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Status Angsuran';

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
        PinjamanStatusController::runangs();
    }
}
