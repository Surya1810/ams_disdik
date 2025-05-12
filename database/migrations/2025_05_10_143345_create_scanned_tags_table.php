<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scanned_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')->constrained('scans')->onDelete('cascade');
            $table->string('kode');
            $table->string('name');
            $table->string('register');
            $table->string('merk');
            $table->string('ukuran')->nullable();
            $table->string('bahan');
            $table->year('tahun_pembelian');
            $table->date('tanggal_pembelian')->nullable();
            $table->string('pabrik')->nullable();
            $table->string('rangka')->nullable();
            $table->string('mesin')->nullable();
            $table->string('polisi')->nullable();
            $table->string('bpkb')->nullable();
            $table->string('nip_pic');
            $table->string('nama_pic');
            $table->string('jabatan_pic');
            $table->string('telp_pic');
            $table->string('asal_perolehan');
            $table->decimal('nilai_perolehan', 15, 2);
            $table->enum('kondisi', ['Baik', 'Perlu Perbaikan', 'Rusak Ringan', 'Rusak Sedang', 'Rusak Berat']);
            $table->date('tanggal_perawatan');
            $table->decimal('harga_perawatan', 15, 2);
            $table->integer('waktu_perawatan');
            $table->string('gedung');
            $table->string('lantai');
            $table->string('ruangan');
            $table->string('detail');
            $table->string('foto_awal')->default('dummy.jpg');
            $table->string('foto_kondisi')->nullable();
            $table->string('status')->nullable(); //dipinjam, dijual, dihibahkan dsb
            $table->text('desc')->nullable(); //kolom tambahan bila diperlukan
            $table->boolean('is_there')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scanned_tags');
    }
};
