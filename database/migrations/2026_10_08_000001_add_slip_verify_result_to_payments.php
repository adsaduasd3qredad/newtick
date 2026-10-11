<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * เพิ่มคอลัมน์เก็บผลการตรวจสอบสลิปอัตโนมัติจาก EasySlip API
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->json('slip_verify_result')->nullable()->after('slip_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('slip_verify_result');
        });
    }
};

