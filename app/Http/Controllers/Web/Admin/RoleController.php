<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Requests;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Flash;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Display a listing of the Role.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $roles = Role::paginate(10);

        return view('admin.roles.index')
            ->with('roles', $roles);
    }

    /**
     * Show the form for creating a new Role.
     *
     * @return Response
     */
    public function create()
    {
        $allPermissions = Permission::all(['id', 'display_name']);
        return view('admin.roles.create')->with('allPermissions', $allPermissions);
    }

    /**
     * Store a newly created Role in storage.
     *
     * @param CreateRoleRequest $request
     *
     * @return Response
     */
    public function store(CreateRoleRequest $request)
    {
        $input = $request->except('perms');

        $role = Role::create($input);

        $role->perms()->sync($request->get('perms')? : []);

        Flash::success('Role saved successfully.');

        return redirect(route('roles.index'));
    }

    /**
     * Display the specified Role.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);

        // $this->authorize($role); // check

        if (empty($role)) {
            Flash::error('Role not found');

            return redirect(route('admin.roles.index'));
        }

        return view('admin.roles.show')->with('role', $role);
    }

    /**
     * Show the form for editing the specified Role.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $role = Role::findOrFail($id);

        // $this->authorize('update', $role); // check

        $perms = array_column($role->perms()->get(['id'])->toArray(), 'id');

        $allPermissions = Permission::all(['id', 'display_name']);

        if (empty($role)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }

        return view('admin.roles.edit')->with(['role' => $role, 'perms' => $perms, 'allPermissions' => $allPermissions]);
    }

    /**
     * Update the specified Role in storage.
     *
     * @param  int              $id
     * @param UpdateRoleRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRoleRequest $request)
    {
        $role = Role::findOrFail($id);

        if (empty($role)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }

        $input = $request->except('perms');
        $role['name'] = $input['name'];
        $role['display_name'] = $input['display_name'];
        $role['description'] = $input['description'];

        $role->save();

        $role->perms()->sync($request->get('perms')? : []);

        Flash::success('Role updated successfully.');

        return redirect(route('roles.index'));
    }

    /**
     * Remove the specified Role from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        // $this->authorize($role); // check

        if (empty($role)) {
            Flash::error('Role not found');

            return redirect(route('roles.index'));
        }

        $role->delete();

        Flash::success('Role deleted successfully.');

        return redirect(route('roles.index'));
    }
}
