<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            // 📌 เช็คก่อนว่าถ้ายังไม่มีคอลัมน์นี้ ถึงจะทำการเพิ่ม
            if (!Schema::hasColumn('movies', 'poster_path')) {
                $table->string('poster_path')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn('poster_path');
        });
    }
};
