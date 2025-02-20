<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Actions\Role\CreateRole;
use App\Actions\Role\UpdateRole;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use App\Http\Requests\RoleFormRequest;
use Spatie\Permission\Models\Permission;


class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $roles = Role::with('permissions')->latest()->get();
        return view('role.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         $permissions = Permission::all();
        // $permission_groups = User::getPermissionGroup();

        return view('role.create',compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
         $request->validate([
             'name' => 'required|unique:roles,name',
         ]);
        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);
        $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name');
        $role->syncPermissions($permissionNames);
//        return $request->all();
        // CreateRole::create($request);

        session()->flash('success', 'Role Created!');
        return redirect()->route('roles.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $permissions = Permission::all();
        $role = Role::with('permissions')->findOrFail($id);
        $data = $role->permissions()->pluck('id')->toArray();
        // $permission_groups = User::getPermissionGroup();

        return view('role.edit',compact('permissions','role','data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update( Request $request, Role $role)
    {



        $request->validate([
            'name' => ['required', 'unique:roles,name,' . $role->id],
        ]);
        $role->update(['name' => $request->name]);
        $permissionNames = Permission::whereIn('id', $request->permissions)->pluck('name');
        $role->syncPermissions($permissionNames);
        session()->flash('success', 'Role Updated!');
        return redirect()->route('roles.index');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        session()->flash('success', 'Role Deleted!');
        return back();

    }
}
