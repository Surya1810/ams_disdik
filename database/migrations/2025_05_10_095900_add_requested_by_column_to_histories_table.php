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
             * Meski pun dibuat tidak berelasi
             * Ini digunakan untuk get history miliki usernya sendiri
             */
            $table->integer('requester_id')->nullable()->after('is_rejected');

            /**
             * Menggunakan payload json, karena:
             * - Agar seragam dengan yang sudah ada
             * - Saat user dihapus, data user di history tetap tercatat
             */
            $table->json('requester_payload')->nullable()->after('requester_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('histories', function (Blueprint $table) {
            $table->dropColumn('requester_id');
            $table->dropColumn('requester_payload');
        });
    }
};
