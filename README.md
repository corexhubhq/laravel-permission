# corexhubhq/laravel-permission

Role, team and permission management for Laravel 11, 12 and 13.

## Features

- Assign roles and teams to users via a polymorphic pivot table (`user_has_models`)
- Per-user direct permissions stored as JSON
- Optional multi-tenant scoping via `system_id`
- Cached permission resolution per request (no repeated queries)
- Publishable config and migrations

---

## Installation

### 1. Require the package

**From Packagist** (once published):
```bash
composer require corexhubhq/laravel-permission

composer update corexhubhq/laravel-permission --no-interaction 2>&1
```

**From the private GitHub repository** (internal use, pin a release tag):
```json
// composer.json
"repositories": [
    { "type": "vcs", "url": "https://github.com/corexhubhq/laravel-permission" }
],
"require": {
    "corexhubhq/laravel-permission": "^1.1"
}
```

**From a local path** (monorepo / development):
```json
// composer.json
"repositories": [
    { "type": "path", "url": "packages/corexhubhq/laravel-permission" }
],
"require": {
    "corexhubhq/laravel-permission": "*"
}
```
```bash
composer update corexhubhq/laravel-permission
```

### 2. Publish config

```bash
php artisan vendor:publish --tag=permission-config
```

This creates `config/permission.php`. Edit the values to match your application before running migrations.

### 3. Publish and run migrations

```bash
php artisan vendor:publish --tag=permission-migrations
php artisan migrate
```

> **Note:** If your application already has the ACL tables (permissions, roles, teams, user_has_models), skip publishing the first migration. Publish only `000002` to add the `system_id` / `permissions` columns to users — or skip it entirely if those columns already exist.

---

## Configuration

`config/permission.php`:

```php
return [
    // Eloquent model representing the authenticated user.
    // The model must use HasPermissionTrait.
    'user_model' => App\Models\User::class,

    // Eloquent model for roles.
    // IMPORTANT: this value is stored in user_has_models.related.
    // If you extend the package Role, point this to your extended class
    // so the filter matches your existing data.
    'role_model' => App\Models\Role::class,

    // Eloquent model for teams. Same rules as role_model.
    'team_model' => App\Models\Team::class,

    // Pivot table name.
    'pivot_table' => 'user_has_models',

    // When true, the migration adds system_id to roles, teams, and users,
    // enabling per-tenant permission scoping.
    // Set to false for single-tenant apps.
    'use_system_scope' => false,

    // JSON column on the users table for per-user direct permissions.
    // Set to null to skip creating this column.
    'user_permissions_column' => 'permissions',
];
```

### `role_model` / `team_model`

These two keys serve a dual purpose:

1. **Used in the default `roles()` / `teams()`** defined by the traits to query the pivot table.
2. **Matched against the `related` column** in `user_has_models` when filtering assignments.

If you extend `Role` or `Team` with your own app model, set these keys to your extended class — otherwise the relationship filter will return no results.

---

## Usage

### Add trait to your User model

```php
use Corexhubhq\LaravelPermission\Traits\HasPermissionTrait;

class User extends Authenticatable
{
    use HasPermissionTrait;
    // HasPermissionTrait includes HasRoleTrait and HasTeamTrait automatically
}
```

### Check permissions

```php
$user->hasPermission('users-edit');   // true / false
$user->hasRole(5);                    // check by role id
$user->hasTeam(3);                    // check by team id
$user->getRoles();                    // array of roles keyed by id
$user->getTeams();                    // array of teams keyed by id
$user->formattedPermissions($raw);    // convert JSON permissions to ['group-action', ...]
```

---

## Relationships

The traits provide default `roles()` and `teams()` methods that query through the pivot table using `config('permission.role_model')` and `config('permission.team_model')`.

**Override when you need extra scoping** (e.g. multi-tenancy):

```php
use App\Models\Role;
use App\Models\Team;

class User extends Authenticatable
{
    use HasPermissionTrait;

    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'user_has_models', 'user_id', 'model_id')
                    ->where('roles.system_id', sessionGetSystemId())
                    ->where('user_has_models.related', Role::class);
    }

    public function teams(): MorphToMany
    {
        return $this->morphToMany(Team::class, 'model', 'user_has_models', 'user_id', 'model_id')
                    ->where('teams.system_id', sessionGetSystemId())
                    ->where('user_has_models.related', Team::class);
    }
}
```

When the method is defined on the model, it takes precedence over the trait default.

---

## Extending models

The package models are plain Eloquent models. Extend them to add application-specific relationships or scopes:

```php
use Corexhubhq\LaravelPermission\Models\Role as PackageRole;

class Role extends PackageRole
{
    public function system()
    {
        return $this->hasOne(System::class, 'id', 'system_id');
    }

    public function getSystemNameAttribute(): ?string
    {
        return $this->system()->first()?->name ?? 'Anonymous system';
    }
}
```

Remember to set `'role_model'` in `config/permission.php` to point to your extended class.

---

## How permissions are resolved

`hasPermission()` merges three sources in order (duplicates removed):

1. **Direct** — `user.permissions` JSON column
2. **Via roles** — `permissions` JSON on each assigned role
3. **Via teams** — `permissions` JSON on each assigned team

Permissions are formatted as `{group}-{action}` strings (e.g. `users-edit`). The resolved list is cached in-memory for the lifetime of the model instance.
