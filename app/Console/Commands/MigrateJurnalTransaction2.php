<?php

namespace App\Console\Commands;

Use App\Http\Controllers\Migration2Controller;
Use App\Http\Controllers\Migration3Controller;
use Illuminate\Console\Command;

class MigrateJurnalTransaction2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jurnal:migrateTransaction2 {month}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate Jurnal Transaction';

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
        Migration3Controller::migrationJurnalTransaction($this->argument('month'));
    }
}
