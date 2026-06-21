<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Permission extends Model
{
    protected $fillable = ['name', 'guard_name', 'display_name', 'description'];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
}
