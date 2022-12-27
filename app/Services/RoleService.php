<?php

namespace App\Services;

use App\Role;
use App\Permission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleService
{
    public function store(Request $request)
    {
        try {
            $role = Role::create($request->validated());
            $role->permissions()->sync($request->input('permissions', []));

            return $role;
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function update(Request $request, Role $role)
    {
        try {
            $role->update($request->all());
            $role->permissions()->sync($request->input('permissions', []));
        } catch (\Throwable $th) {
            throw new \Exception($th->getMessage(), Response::HTTP_BAD_REQUEST);
        }
    }

    public function removeMany(...$ids)
    {
        Role::whereIn('id', $ids)->delete();
    }
}
