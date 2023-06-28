<?php

namespace Database\Seeders;

use App\Models\Permission;
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
      
        $developer_create = new User();
        $developer_create->username = 'teacher';
        $developer_create->email = 'teacher@test.com';
        $developer_create->password = Hash::make(123456);
        $developer_create->type = 1;
        $developer_create->save();
       
        $developer_delete = new User();
        $developer_delete->username = 'student';
        $developer_delete->email = 'student@test.com';
        $developer_delete->password = Hash::make(123456);
        $developer_delete->type = 2;
        $developer_delete->save();

    }
}
