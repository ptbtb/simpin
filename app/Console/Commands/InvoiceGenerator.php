<?php

namespace App\Console\Commands;

use App\Managers\InvoiceManager;
use Illuminate\Console\Command;

class InvoiceGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate invoice every month';

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
    public function handle(InvoiceManager $invoiceManager)
    {
        $invoiceManager->generateInvoiceMonthly();
    }
}
