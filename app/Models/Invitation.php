<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invitation extends Model
{
    protected $fillable = ['group_id', 'invited_by', 'email', 'status'];

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id'); // Confirme se a chave está correta
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'invited_by', 'id'); // Confirme se a chave está correta
    }
}