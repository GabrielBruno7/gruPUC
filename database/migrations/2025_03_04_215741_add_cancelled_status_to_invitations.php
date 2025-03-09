<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Alterando o campo 'status' para adicionar o valor 'cancelado'
        Schema::table('invitations', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'rejected', 'cancelled'])->default('pending')->change();
        });
    }

    public function down()
    {
        // Revertendo a alteração, removendo 'cancelled' do enum
        Schema::table('invitations', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->change();
        });
    }
};
