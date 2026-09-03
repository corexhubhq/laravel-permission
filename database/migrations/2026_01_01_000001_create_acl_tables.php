<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('description')->nullable();
                $table->string('type')->nullable();
                $table->json('actions')->nullable();
            });
        }

        if (!Schema::hasTable('permission_actions')) {
            Schema::create('permission_actions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('group');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('description')->nullable();
                if (config('permission.use_system_scope')) {
                    $table->unsignedBigInteger('system_id')->nullable();
                }
                $table->json('permissions')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('teams')) {
            Schema::create('teams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('description')->nullable();
                if (config('permission.use_system_scope')) {
                    $table->unsignedBigInteger('system_id')->nullable();
                }
                $table->json('permissions')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_has_roles')) {
            Schema::create('user_has_roles', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('role_id');

                $table->index('user_id', 'idx_uhr_user_id');
                $table->index('role_id', 'idx_uhr_role_id');
                $table->unique(['user_id', 'role_id'], 'uq_uhr_assignment');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_has_models');
        Schema::dropIfExists('teams');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('permission_actions');
        Schema::dropIfExists('permissions');
    }
};
