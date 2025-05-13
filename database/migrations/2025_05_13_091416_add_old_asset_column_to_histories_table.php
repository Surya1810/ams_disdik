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
        Schema::table('histories', function (Blueprint $table) {
            /**
             * Digunakan untuk get data lama
             * Karena ini histories, artinya saat asset
             * dilelang/dihapus dari tabel assets,
             * harus tetap ada muncul di setiap menu history
             *
             * column old_values yang ada saat ini di tabel histories,
             * belum bisa mengatasi hal itu,
             * karena sifatnya dinamis tergantung data lama yang berubah.
             * Sedangkan yang diperlukan adalah statis data lama.
             */
            $table->json('old_asset')->after('new_values')->nullable();

            /**
             * Belum ada relasi dari histories ke approval dengan benar
             * sebelum ada ini, relasi malah langsung mengambil asset_id
             * hasil sebelumnya: saat asset yang sama diajukan berulang kali
             * kemudian di disposal di tolak, maka
             * rejection_note yang diambil selalu yang pertama.
             */
            $table->integer('approval_id')->after('user_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('histories', function (Blueprint $table) {
            $table->dropColumn('old_asset');
        });
    }
};
