<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
            $table->boolean('is_best_seller')->default(false)->after('is_available');
            $table->text('details')->nullable()->after('description');
        });
    }

    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropColumn(['image', 'is_best_seller', 'details']);
        });
    }
};