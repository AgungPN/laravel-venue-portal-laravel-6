<?php

namespace App\Repositories;

use App\EventType;
use App\Permission;

class RoleRepository
{
    public function getPermissions()
    {
        return Permission::all()->pluck('title', 'id');
    }

}
