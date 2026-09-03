<?php

return [
    /*
     * The Eloquent model representing the user.
     * Must use HasPermissionTrait (which includes HasRoleTrait and HasTeamTrait).
     */
    'user_model' => App\Models\User::class,

    /*
     * The Eloquent model for roles.
     * Override with your own class when extending the package model.
     * This value is also stored in the `related` column of the pivot table —
     * keep it consistent with any existing data.
     */
    'role_model' => Corexhubhq\LaravelPermission\Models\Role::class,

    /*
     * The Eloquent model for teams.
     * Same rules as role_model above.
     */
    'team_model' => Corexhubhq\LaravelPermission\Models\Team::class,

    /*
     * The pivot table that links users to roles and teams.
     */
    'pivot_table' => 'user_has_models',

    /*
     * When true, the ACL migration creates system_id on roles, teams, and users,
     * enabling multi-tenant permission scoping.
     */
    'use_system_scope' => false,

    /*
     * JSON column on the users table that holds per-user direct permissions.
     * Set to null to skip adding this column in the migration.
     */
    'user_permissions_column' => 'permissions',
];
