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
        //
        Schema::table('movies', function (Blueprint $table) {
            $table->string('rating')->nullable()->after('description');
            $table->string('language')->nullable()->after('duration_minutes');
            $table->string('trailer_url')->nullable()->after('poster_path');
            $table->integer('total_seats')->default(160)->after('language');
            $table->enum('status', ['draft', 'publish'])->default('draft')->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn(['rating', 'language', 'trailer_url', 'total_seats', 'status']);
        });
    }
};
