<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
        // Defina a tabela personalizada
        protected $table = 'password_resets'; // Nome correto da tabela

        // Defina os campos que podem ser preenchidos
        protected $fillable = ['email', 'token', 'created_at'];
    
        // Se necessário, desative os timestamps
        public $timestamps = false;
}
