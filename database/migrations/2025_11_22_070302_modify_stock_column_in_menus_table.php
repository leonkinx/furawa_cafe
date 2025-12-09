<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            // Ubah column stock jadi nullable dengan default null
            $table->integer('stock')->nullable()->default(null)->change();
        });
    }

    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            // Kembalikan ke not null dengan default 0
            $table->integer('stock')->nullable(false)->default(0)->change();
        });
    }
};