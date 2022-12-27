<?php

namespace App\Http\Controllers\Admin;

use App\Role;
use App\Permission;
use App\Services\RoleService;
use App\Http\Controllers\Controller;
use App\Repositories\RoleRepository;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\MassDestroyRoleRequest;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response;

class RolesController extends Controller
{

    private RoleService $roleService;
    private RoleRepository $roleRepository;
    private Collection $permissions;

    public function __construct(RoleService $roleService, RoleRepository $roleRepository)
    {
        $this->roleService = $roleService;
        $this->roleRepository = $roleRepository;

        $this->permissions = $this->roleRepository->getPermissions();
    }

    public function index()
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.roles.index', ['roles' => Role::all()]);
    }

    public function create()
    {
        abort_if(Gate::denies('role_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.roles.create', ['permissions' => $this->permissions]);
    }

    public function store(StoreRoleRequest $request)
    {
        try {
            $this->roleService->store($request);
        } catch (\Throwable $th) {
            abort($th->getCode(), $th->getMessage());
        }
        return redirect()->route('admin.roles.index');
    }

    public function edit(Role $role)
    {
        abort_if(Gate::denies('role_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->load('permissions');

        return view('admin.roles.edit', [
            'permissions' => $this->permissions,
            'role' => $role
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role)
    {
        try {
            $this->roleService->update($request, $role);
        } catch (\Throwable $th) {
            abort($th->getCode(), $th->getMessage());
        }
        return redirect()->route('admin.roles.index');
    }

    public function show(Role $role)
    {
        abort_if(Gate::denies('role_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->load('permissions');

        return view('admin.roles.show', compact('role'));
    }

    public function destroy(Role $role)
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->delete();

        return back();
    }

    public function massDestroy(MassDestroyRoleRequest $request)
    {
        $this->roleService->removeMany(request('ids'));
        return response(null, Response::HTTP_NO_CONTENT);
    }
}
