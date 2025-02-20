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

    protected $fillable = ['id', 'name', 'course_id', 'owner_id', 'period'];

    protected $casts = [
        'id' => 'string',
        'period' => 'integer',
        'name' => 'string',
        'course_id' => 'string',
        'owner_id' => 'string',
    ];

    // Relacionamento com o curso
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    // Relacionamento com o usuário dono do grupo
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
            if (empty($group->id)) {
                $group->id = (string) Str::uuid();
            }
        });
    }
}

