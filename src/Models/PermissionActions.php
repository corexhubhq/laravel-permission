<?php

namespace Corexhubhq\LaravelPermission\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionActions extends Model
{
    protected $table = 'permission_actions';
    protected $fillable = ['name', 'group'];
}
