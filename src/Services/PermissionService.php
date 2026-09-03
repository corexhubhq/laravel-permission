<?php

namespace Corexhubhq\LaravelPermission\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Corexhubhq\LaravelPermission\Models\Permission;

class PermissionService
{
    public function preparePermissionData(array $input): array
    {
        $actions = [];
        foreach (Arr::get($input, 'actions', []) as $action => $value) {
            if (!$value) {
                continue;
            }
            $actions[] = $action;
        }

        return [
            'name'        => Str::lower(Arr::get($input, 'name', '')),
            'description' => ucfirst(Arr::get($input, 'description', '')),
            'actions'     => $actions ?: null,
        ];
    }

    public function getActionsByGroup(string $groupPermission): array
    {
        if (!$groupPermission) {
            return [];
        }

        $permission = Permission::getActionsByGroup($groupPermission);

        return $permission?->getActions() ?? [];
    }
}
