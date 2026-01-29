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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_id')->unique(); // Mã hóa đơn: INV-YYMM-XXX
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2); // Số tiền thu
            $table->enum('payment_type', ['cash', 'transfer'])->default('cash');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('note')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
