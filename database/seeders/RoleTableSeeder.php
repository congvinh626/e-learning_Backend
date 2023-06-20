<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dev_role = new Role();
        $dev_role->slug = 'developer';
        $dev_role->name = 'Front-end Developer';
        $dev_role->save();

        $manager_role = new Role();
        $manager_role->slug = 'manager';
        $manager_role->name = 'Assistant Manager';
        $manager_role->save();
    }
}
