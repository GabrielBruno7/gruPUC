<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_group', function (Blueprint $table) {
            $table->renameColumn('uuid', 'id');
        });
    }

    public function down(): void
    {
        Schema::table('user_group', function (Blueprint $table) {
            $table->renameColumn('id', 'uuid');
        });
    }
};
