<?php

namespace Corexhubhq\LaravelPermission\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $table = 'teams';
    protected $fillable = ['name', 'description', 'system_id', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function getId(): mixed
    {
        return $this->id;
    }

    public function scopeBySystem($query, $systemId = 1)
    {
        return $query->where('system_id', $systemId);
    }
}
