<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::create([
            'name' => 'Administrator',
        ]);
        $role = Role::create([
            'name' => 'Operator',
        ]);
        $role = Role::create([
            'name' => 'User',
        ]);
    }
}
