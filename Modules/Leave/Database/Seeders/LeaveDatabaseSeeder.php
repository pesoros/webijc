<?php

namespace Modules\Leave\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class LeaveDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

         $this->call(LeaveTypeTableSeeder::class);
         $this->call(LeaveDefineTableSeeder::class);
         $this->call(ApplyLeaveTableSeeder::class);
    }
}
