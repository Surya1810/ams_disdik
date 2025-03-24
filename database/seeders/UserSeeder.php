<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'role_id' => '1',
            'kecamatan_id' => '1',
            'name' => 'Partnership',
            'password' => bcrypt('Jayaselalu28@'),
        ]);
        $user = User::create([
            'role_id' => '2',
            'kecamatan_id' => '2',
            'name' => 'Dispora',
            'password' => bcrypt('Dispora'),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '3',
            'name' => 'Cigugur',
            'password' => bcrypt('Cigugur'),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '4',
            'name' => 'Cijulang',
            'password' => bcrypt('Cijulang'),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '5',
            'name' => 'Cimerak',
            'password' => bcrypt('Cimerak'),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '6',
            'name' => 'Kalipucang',
            'password' => bcrypt('Kalipucang'),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '7',
            'name' => 'Langkaplancar',
            'password' => bcrypt('Langkaplancar'),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '8',
            'name' => 'Mangunjaya',
            'password' => bcrypt('Mangunjaya'),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '9',
            'name' => 'Padaherang',
            'password' => bcrypt('Padaherang'),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '10',
            'name' => 'Pangandaran',
            'password' => bcrypt('Pangandaran'),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '11',
            'name' => 'Parigi',
            'password' => bcrypt('Parigi'),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '12',
            'name' => 'Sidamulih',
            'password' => bcrypt('Sidamulih'),
        ]);
    }
}
