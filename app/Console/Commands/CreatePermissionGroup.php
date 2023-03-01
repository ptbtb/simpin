<?php

namespace App\Console\Commands;

use App\Models\PermissionGroup;
use Illuminate\Console\Command;

class CreatePermissionGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:permissiongroup {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permission group. Use double quotes for permission group names that are more than 1 word';

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
        PermissionGroup::create(['name' => $this->argument('name')]);
        echo "added permission group ".$this->argument('name');
    }
}
