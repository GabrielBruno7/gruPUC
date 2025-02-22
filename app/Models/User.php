<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['id', 'name', 'email', 'password', 'role','period', 'course_id', 'group_id'];

    protected $hidden = ['password'];

    protected $casts = [
        'id' => 'string',
        'role' => 'string',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->id = (string) Str::uuid();
        });
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'user_group')
            ->withPivot('status', 'id')  // Incluindo tanto 'status' quanto 'id'
            ->withTimestamps();
    }
}
