<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_group', function (Blueprint $table) {
            $table->dropPrimary();
            $table->uuid('id')->primary()->default(Str::uuid())->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_group', function (Blueprint $table) {
            $table->increments('id')->primary()->change();
        });
    }
};
