<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
            $table->string('qr_code')->nullable();
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
        });
    }

    public function down()
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['status', 'qr_code']);
        });

        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
};