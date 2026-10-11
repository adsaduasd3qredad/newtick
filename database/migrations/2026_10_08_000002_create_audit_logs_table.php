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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_role')->nullable();
            $table->string('action'); // เช่น quick_sell, confirm_payment, void_booking, status_change, login
            $table->foreignId('booking_id')->nullable()->constrained()->nullOnDelete();
            $table->string('order_reference')->nullable(); // #15 หรือ QR Ref
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('payment_method')->nullable(); // counter, qr_code
            $table->string('description'); // ข้อความอธิบายอย่างละเอียด เช่น "POS สมชาย ขายตั๋ว Walk-in 2 ที่นั่ง (เงินสด 100 บาท)"
            $table->json('properties')->nullable(); // บันทึกรายละเอียด JSON เช่น ที่นั่ง, สลิป ฯลฯ
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['action', 'created_at']);
            $table->index('user_id');
            $table->index('booking_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

