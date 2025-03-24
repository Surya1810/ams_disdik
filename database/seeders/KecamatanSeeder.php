<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kecamatan = Kecamatan::create([
            'name' => 'Admin',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Dispora',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Cigugur',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Cijulang',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Cimerak',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Kalipucang',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Langkaplancar',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Mangunjaya',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Padaherang',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Pangandaran',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Parigi',
        ]);
        $kecamatan = Kecamatan::create([
            'name' => 'Sidamulih',
        ]);
    }
}
