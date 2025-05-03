<?php

namespace Database\Seeders;

use App\Models\User;
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
            'password' => bcrypt(getenv('USER_ROLE_1')),
        ]);
        $user = User::create([
            'role_id' => '2',
            'kecamatan_id' => '2',
            'name' => 'Dispora',
            'password' => bcrypt(getenv('USER_ROLE_2')),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '3',
            'name' => 'Cigugur',
            'password' => bcrypt(getenv('USER_ROLE_3_1')),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '4',
            'name' => 'Cijulang',
            'password' => bcrypt(getenv('USER_ROLE_3_2')),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '5',
            'name' => 'Cimerak',
            'password' => bcrypt(getenv('USER_ROLE_3_3')),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '6',
            'name' => 'Kalipucang',
            'password' => bcrypt(getenv('USER_ROLE_3_4')),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '7',
            'name' => 'Langkaplancar',
            'password' => bcrypt(getenv('USER_ROLE_3_5')),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '8',
            'name' => 'Mangunjaya',
            'password' => bcrypt(getenv('USER_ROLE_3_6')),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '9',
            'name' => 'Padaherang',
            'password' => bcrypt(getenv('USER_ROLE_3_7')),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '10',
            'name' => 'Pangandaran',
            'password' => bcrypt(getenv('USER_ROLE_3_8')),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '11',
            'name' => 'Parigi',
            'password' => bcrypt(getenv('USER_ROLE_3_9')),
        ]);
        $user = User::create([
            'role_id' => '3',
            'kecamatan_id' => '12',
            'name' => 'Sidamulih',
            'password' => bcrypt(getenv('USER_ROLE_3_10')),
        ]);
    }
}
