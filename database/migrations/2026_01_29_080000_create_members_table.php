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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('member_id')->unique(); // Format: YYMM + seq (e.g., 2601001)
            $table->string('name');
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('mobile', 20);
            $table->string('blood_group', 5)->nullable(); // A+, A-, B+, B-, O+, O-, AB+, AB-
            $table->text('address')->nullable();
            $table->string('photo')->nullable(); // Path to photo file
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('status')->default(true); // Active/Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
