<?php

namespace Corexhubhq\LaravelPermission\Traits;

trait HasPermissionTrait
{
    use HasRoleTrait, HasTeamTrait;

    private ?array $cachedPermissions = null;

    public function hasPermission(string $permission): bool
    {
        if ($this->cachedPermissions === null) {
            $this->cachedPermissions = $this->resolvePermissions();
        }

        return in_array($permission, $this->cachedPermissions);
    }

    private function resolvePermissions(): array
    {
        $all = $this->formattedPermissions($this->permissions);

        foreach ($this->getRoles() as $role) {
            $all = array_merge($all, $this->formattedPermissions($role['permissions'] ?? []));
        }

        foreach ($this->getTeams() as $team) {
            $all = array_merge($all, $this->formattedPermissions($team['permissions'] ?? []));
        }

        return array_unique($all);
    }

    public function formattedPermissions(?array $permissions): array
    {
        if (!$permissions) {
            return [];
        }

        return array_reduce(
            array_keys($permissions),
            fn(array $carry, string $key) => array_merge($carry, array_map(fn(string $action) => "$key-$action", $permissions[$key])),
            []
        );
    }
}
