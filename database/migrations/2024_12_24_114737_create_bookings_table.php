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
            $table->unsignedBigInteger('driver_id');
            $table->date('booking_date')->nullable();
            $table->time('booking_time')->nullable();
            $table->text('from')->nullable();
            $table->text('to')->nullable();
            $table->float('fare')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->text('cancel_reason')->nullable();
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
