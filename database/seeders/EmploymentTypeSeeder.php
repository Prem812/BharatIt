<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // employment_types

        DB::table('employment_types')->delete();
        $employment_types = array(
            array('id' => 1, 'name' => "Remote"),
            array('id' => 2, 'name' => "Work from home"),
            array('id' => 3, 'name' => "Office"),
            array('id' => 4, 'name' => "On-site"),
            array('id' => 5, 'name' => "Hybrid"),
        );
        DB::table('employment_types')->insert($employment_types);
    }
}
