<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY visitor_type ENUM('individual', 'school', 'company', 'government') NOT NULL DEFAULT 'individual'");
        } elseif (DB::getDriverName() === 'sqlite') {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('visitor_type')->default('individual')->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY visitor_type ENUM('individual', 'company', 'government') NOT NULL DEFAULT 'individual'");
        } elseif (DB::getDriverName() === 'sqlite') {
            Schema::table('bookings', function (Blueprint $table) {
                $table->string('visitor_type')->default('individual')->change();
            });
        }
    }
};
