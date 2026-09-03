<?php

namespace Corexhubhq\LaravelPermission\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['name', 'description', 'system_id', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function getId(): mixed
    {
        return $this->id;
    }

    public function users()
    {
        return $this->morphedByMany(
            config('permission.user_model'),
            'model',
            config('permission.pivot_table'),
            'model_id',
            'user_id'
        );
    }
}
