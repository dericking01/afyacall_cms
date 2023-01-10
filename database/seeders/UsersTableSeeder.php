<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'name' => 'Administrator',
            'username' => 'Administrator',
            'mobile' => '255746805383',
            'email' => 'julius@afyacall.co.tz',
            'password' => bcrypt('Afya@2022')
        ]);

        $user->assignRole('administrator');
    }
}
