<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['id','name', 'course', 'owner_id'];

    protected $casts = [
        'id' => 'string',
        'name' => 'string',
        'course' => 'string',
    ];

    // Definir o relacionamento com o usuário dono do grupo
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // Relacionamento com os membros do grupo
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    // Garante que o ID seja um UUID antes de salvar o grupo
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($group) {
            // Gera um UUID se o campo id não estiver definido
            if (empty($group->id)) {
                $group->id = (string) Str::uuid();
            }
        });
    }
}
