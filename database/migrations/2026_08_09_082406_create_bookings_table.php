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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('showtime_id')->constrained()->cascadeOnDelete();
            $table->string('booker_name');
            $table->string('booker_email');
            $table->string('booker_phone')->nullable();
            $table->enum('visitor_type', ['individual', 'company', 'government'])->default('individual');
            $table->integer('quantity');
            $table->decimal('total_amount', 10, 2);
            $table->string('discount_code')->nullable();
            $table->enum('status', ['pending', 'awaiting_payment', 'paid', 'redeemed', 'expired', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['qr_code', 'counter'])->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('qr_payment_ref')->nullable();
            $table->string('qr_ticket_ref')->unique()->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
