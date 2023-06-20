<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dev_role = Role::where('slug','developer')->first();
        $manager_role = Role::where('slug', 'manager')->first();
        $dev_create_perm = Permission::where('slug','create-tasks')->first();
        $dev_delete_perm = Permission::where('slug','delete-tasks')->first();
        $manager_perm = Permission::where('slug','view-users')->first();

        $developer_create = new User();
        $developer_create->name = 'I am developer - Create';
        $developer_create->email = 'dev_create@test.com';
        $developer_create->password = Hash::make(123456);
        $developer_create->save();
        $developer_create->roles()->attach($dev_role);
        $developer_create->permissions()->attach($dev_create_perm);

        $developer_delete = new User();
        $developer_delete->name = 'I am developer - Delete';
        $developer_delete->email = 'dev_delete@test.com';
        $developer_delete->password = Hash::make(123456);
        $developer_delete->save();
        $developer_delete->roles()->attach($dev_role);
        $developer_delete->permissions()->attach($dev_delete_perm);

        $developer_full = new User();
        $developer_full->name = 'I am developer - Full';
        $developer_full->email = 'dev_full@test.com';
        $developer_full->password = Hash::make(123456);
        $developer_full->save();
        $developer_full->roles()->attach($dev_role);
        $developer_full->permissions()->attach($dev_create_perm);
        $developer_full->permissions()->attach($dev_delete_perm);

        $manager = new User();
        $manager->name = 'I am manager';
        $manager->email = 'manager@test.com';
        $manager->password = Hash::make(123456);
        $manager->save();
        $manager->roles()->attach($manager_role);
        $manager->permissions()->attach($manager_perm);

        $manager1 = new User();
        $manager1->name = 'I am manager1';
        $manager1->email = 'manager1@test.com';
        $manager1->password = Hash::make(123456);
        $manager1->save();
        $manager1->roles()->attach($manager_role);
    }
}
