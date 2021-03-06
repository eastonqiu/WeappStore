<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Requests;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Flash;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Display a listing of the User.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $users = User::paginate(10);

        return view('admin.users.index')
            ->with('users', $users);
    }

    /**
     * Show the form for creating a new User.
     *
     * @return Response
     */
    public function create()
    {
        $allRoles = Role::all(['id', 'display_name']);
        return view('admin.users.create')->with('allRoles', $allRoles);
    }

    /**
     * Store a newly created User in storage.
     *
     * @param CreateUserRequest $request
     *
     * @return Response
     */
    public function store(CreateUserRequest $request)
    {
        $input = $request->except('roles');

        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);

        $user->roles()->sync($request->get('roles')? : []);

        Flash::success('User saved successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Display the specified User.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        // $this->authorize($user); // check

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('admin.users.index'));
        }

        return view('admin.users.show')->with('user', $user);
    }

    /**
     * Show the form for editing the specified User.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);

        // $this->authorize('update', $user); // check

        $roles = array_column($user->roles()->get(['id'])->toArray(), 'id');

        $allRoles = Role::all(['id', 'display_name']);

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        return view('admin.users.edit')->with(['user' => $user, 'roles' => $roles, 'allRoles' => $allRoles]);
    }

    /**
     * Update the specified User in storage.
     *
     * @param  int              $id
     * @param UpdateUserRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateUserRequest $request)
    {
        $user = User::findOrFail($id);

        // $this->authorize($user); // check

        if (empty($user)) {
            Flash::error('User not found');
            return redirect(route('users.index'));
        }

        $input = $request->except('roles');
        $user['email'] = $input['email'];
        $user['name'] = $input['name'];
        if(isset($input['password'])) {
            $user['password'] = bcrypt($input['password']);
        }

        $user->update($input);

        $user->roles()->sync($request->get('roles')? : []);

        Flash::success('User updated successfully.');

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified User from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // $this->authorize($user); // check

        if (empty($user)) {
            Flash::error('User not found');

            return redirect(route('users.index'));
        }

        // User::destroy($id);
        $user->delete();

        Flash::success('User deleted successfully.');

        return redirect(route('users.index'));
    }
}
