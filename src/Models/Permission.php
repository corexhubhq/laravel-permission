<?php

namespace Corexhubhq\LaravelPermission\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int    $id
 * @property string $name
 * @property string|null $description
 * @property string|null $type
 * @property array|null  $actions
 */
class Permission extends Model
{
    protected $table = 'permissions';
    protected $fillable = ['name', 'description', 'type', 'actions'];

    public $timestamps = false;

    protected $casts = [
        'actions' => 'array',
    ];

    public function getId(): mixed
    {
        return $this->id;
    }

    public function getActions(): array
    {
        return $this->actions ?? [];
    }

    public static function getActionsByGroup(string $permission): ?static
    {
        if (empty($permission)) {
            return null;
        }

        return static::where('name', $permission)->first();
    }
}
