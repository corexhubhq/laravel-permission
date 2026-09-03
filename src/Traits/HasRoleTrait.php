<?php

namespace Corexhubhq\LaravelPermission\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasRoleTrait
{
    /**
     * Default relationship using package models from config.
     * Override this method on the consuming model to add extra scopes
     * (e.g. system_id filtering for multi-tenancy).
     */
    public function roles(): MorphToMany
    {
        $roleModel  = config('permission.role_model');
        $pivotTable = config('permission.pivot_table');

        return $this->morphToMany($roleModel, 'model', $pivotTable, 'user_id', 'model_id')
                    ->where("$pivotTable.related", $roleModel);
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles('id'));
    }

    public function getRoles(?string $pluck = null): array
    {
        $roles = $this->roles()->get();
        if ($roles->isEmpty()) {
            return [];
        }

        if ($pluck) {
            return $roles->pluck($pluck)->toArray();
        }

        return $roles->keyBy('id')->toArray();
    }
}
