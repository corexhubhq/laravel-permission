<?php

namespace Corexhubhq\LaravelPermission\Models;

use Illuminate\Database\Eloquent\Model;

class UserHasModel extends Model
{
    protected $table = 'user_has_models';
    protected $fillable = ['user_id', 'model_type', 'model_id', 'related'];

    public $timestamps = false;
}
