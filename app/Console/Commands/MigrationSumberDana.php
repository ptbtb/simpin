<?php

namespace App\Console\Commands;

use App\Models\Code;
use App\Models\PermissionGroup;
use App\Models\SumberDana;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class MigrationSumberDana extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sumberdana:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migration sumberdana from static to dynamic';

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
        $dataMigration = [
            [
                'name' => 'KAS',
                'detail' => [
                    '101.01.102'
                ]
            ],
            [
                'name' => 'BANK',
                'detail' => [
                    '102.01.102',
                    '102.01.105',
                    '102.03.105',
                    '102.06.000',
                    '102.07.000',
                    '102.08.000',
                    '102.09.000',
                    '102.11.000',
                    '102.12.000',
                    '102.13.000',
                    '102.14.000',
                    '102.15.000',
                    '102.16.000',
                    '102.17.000',
                    '102.18.000',
                    '102.19.000',
                    '102.99.999',
                    '102.91.001',
                ],
            ],
            [
                'name' => 'UTIP',
                'detail' => [
                    '404.02.000',
                    '404.07.000',
                    '404.08.000',
                    '404.39.000',
                    '405.01.000'
                ],
            ],
            [
                'name' => 'SIMPANAN',
                'detail' => [
                    '409.01.000',
                    '409.03.000',
                    '402.01.000',
                ],
            ],
        ];

        foreach ($dataMigration as $dataSumberDana)
        {
            // echo 'Createing sumber dana with name '.$dataSumberDana['name'].'\n';
            $sumberDana = new SumberDana();
            $sumberDana->name = $dataSumberDana['name'];
            $sumberDana->save();
            // echo 'sumber dana created\n';

            // echo 'starting create detail code for sumber dana ' .$dataSumberDana['name'];
            foreach ($dataSumberDana['detail'] as $detailCode)
            {
                // echo'..';
                $code = Code::where('CODE', 'like', $detailCode)->first();
                if($code)
                {
                    $code->sumber_dana_id = $sumberDana->id;
                    $code->save();
                }
            }
            // echo 'create detail finished \n';
        }

        // create permission group
        $permissionGroup = new PermissionGroup();
        $permissionGroup->name = "Sumber Dana";
        $permissionGroup->save();

        $dataPermission = [
            'view sumber dana',
            'add sumber dana',
            'edit sumber dana',
            'delete sumber dana'
        ];

        $sequence = 1;
        foreach ($dataPermission as $data)
        {
            $permission = Permission::create(['name' => $data]);
            $permission->permissions_group_id = $permissionGroup->id;
            $permission->sequence = $sequence;
            $permission->save();
            $sequence++;
        }

        echo 'migration finished';
    }
}
