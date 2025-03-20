<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('group_id');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->uuid('invited_by');
            $table->foreign('invited_by')->references('id')->on('users')->onDelete('cascade');
            $table->string('email');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('invitations');
    }
};