<?php

namespace App\Console\Commands;

use App\Models\PermissionGroup;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class CreatePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:permission {name} {permissiongroup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Added permission. Use double quotes for permission group names that are more than 1 word';

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
        $group = PermissionGroup::where('name', $this->argument('permissiongroup'))
                                ->first();
        
        if (is_null($group))
        {
            $group = PermissionGroup::create(['name' => $this->argument('permissiongroup')]);
            echo "added permission group ".$this->argument('name')."\n";
        }

        $permission = Permission::where('name', 'like', "%".$this->argument('name')."%")->first();
        if ($permission)
        {
            echo "Permission ".$this->argument('name') ." already exist\n";
        } 
        else 
        {
            Permission::create(['name' => $this->argument('name'), 'permissions_group_id' => $group->id]);
            echo "added permission ".$this->argument('name')."\n";
        }
    }
}
