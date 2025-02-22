<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_group', function (Blueprint $table) {
            // Verifica se 'uuid' existe antes de renomear
            if (Schema::hasColumn('user_group', 'uuid')) {
                $table->renameColumn('uuid', 'id');
            }

            // Remove a chave primária antiga (se necessário)
            $table->dropPrimary();

            // Altera 'id' para ser do tipo UUID e chave primária
            $table->uuid('id')->primary()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_group', function (Blueprint $table) {
            // Retorna 'id' para auto-incremento, se necessário
            $table->dropPrimary();
            $table->bigIncrements('id')->change();
        });
    }
};
