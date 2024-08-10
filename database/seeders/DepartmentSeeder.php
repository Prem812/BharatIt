<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->delete();
        $departments = array(
            array('id' => 1, 'name' => "Laravel"),
            array('id' => 2, 'name' => "Code Igniter"),
            array('id' => 3, 'name' => "React"),
            array('id' => 4, 'name' => "NodeJs"),
            array('id' => 5, 'name' => "Flutter"),
            array('id' => 6, 'name' => "Frontend"),
            array('id' => 7, 'name' => "Backend"),
            array('id' => 8, 'name' => "Mobile"),
            array('id' => 9, 'name' => "Design"),
            array('id' => 10, 'name' => "DevOps"),
            array('id' => 11, 'name' => "Java Script"),
        );
        DB::table('departments')->insert($departments);
    }
}
