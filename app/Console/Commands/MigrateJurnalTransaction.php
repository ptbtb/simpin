<?php

namespace App\Console\Commands;

Use App\Http\Controllers\MigrationController;
use Illuminate\Console\Command;

class MigrateJurnalTransaction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jurnal:migrateTransaction {month}';

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
        MigrationController::migrationJurnalTransaction($this->argument('month'));
    }
}
