<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserGroup extends Model
{
    protected $table = 'user_group';
    public $incrementing = false;
    protected $primaryKey = 'id';
    protected $fillable = ['id', 'user_id', 'group_id'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($userGroup) {
            if (empty($userGroup->id)) {
                $userGroup->id = (string) Str::uuid();
            }
        });
    }
}