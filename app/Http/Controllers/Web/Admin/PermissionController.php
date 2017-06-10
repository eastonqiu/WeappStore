<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Requests;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Permission;
use Illuminate\Http\Request;
use Flash;
use App\Http\Controllers\Controller;

class PermissionController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Display a listing of the Permission.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $permissions = Permission::paginate(10);

        return view('admin.permissions.index')
            ->with('permissions', $permissions);
    }

    /**
     * Show the form for creating a new Permission.
     *
     * @return Response
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created Permission in storage.
     *
     * @param CreatePermissionRequest $request
     *
     * @return Response
     */
    public function store(CreatePermissionRequest $request)
    {
        $input = $request->all();

        $permission = Permission::create($input);

        Flash::success('Permission saved successfully.');

        return redirect(route('permissions.index'));
    }

    /**
     * Display the specified Permission.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $permission = Permission::findOrFail($id);

        // $this->authorize($permission); // check

        if (empty($permission)) {
            Flash::error('Permission not found');

            return redirect(route('admin.permissions.index'));
        }

        return view('admin.permissions.show')->with('permission', $permission);
    }

    /**
     * Show the form for editing the specified Permission.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        if (empty($permission)) {
            Flash::error('Permission not found');

            return redirect(route('permissions.index'));
        }

        return view('admin.permissions.edit')->with('permission', $permission);

        $permission = Permission::findOrFail($id);
    }

    /**
     * Update the specified Permission in storage.
     *
     * @param  int              $id
     * @param UpdatePermissionRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePermissionRequest $request)
    {
        $permission = Permission::Fail($id);

        if (empty($permission)) {
            Flash::error('Permission not found');

            return redirect(route('permissions.index'));
        }

        $input = $request->except('perms');
        $user['name'] = $input['name'];
        $user['display_name'] = $input['display_name'];

        $permission->save();

        Flash::success('Permission updated successfully.');

        return redirect(route('permissions.index'));
    }

    /**
     * Remove the specified Permission from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        // $this->authorize($permission); // check

        if (empty($permission)) {
            Flash::error('Permission not found');

            return redirect(route('permissions.index'));
        }

        Permission::destroy($id);

        Flash::success('Permission deleted successfully.');

        return redirect(route('permissions.index'));
    }
}
