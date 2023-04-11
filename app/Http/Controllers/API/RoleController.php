<?php

namespace App\Http\Controllers\API;

use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
class RoleController extends Controller
{
    
    /**
     * 
     */
    // public function __construct(){
    //     $this->middleware('admin');
    // }

    /**    
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()   
    {
        //
        $roles = Role::all();
        return response()->json($roles, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'description' => 'required|min:5'
        ]);
        $role = new Role();
        $role->name = $request->name;
        $role->description = $request->description;
        $role->save();
        return response()->json($role, 201);
        
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
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['error' => 'Role not found'], 404);
        }
        return response()->json($role, 200);    
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $isRoleExist = Role::where('id', $id)->exists();
        if($isRoleExist){
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
            ]);
            if($validator->fails()){
                return response()->json($validator->errors());
            }
            $role = Role::find($id);
            $role->name = $request->name;
            $role->description = $request->description;
            $role->save();
            return response()->json([
                'message' => 'Role succesfull Updated',
                'role' => $role
            ], 201);
        }else{
            return response()->json([
                'success'=> false,
                'message'=> 'Fail to update',
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'success'=>false,
                'message' => 'Role not found'
            ], 404);
        }
        $role->delete();
        return response()->json([
            'success'=>true,
            'message' => 'Role deleted'
        ], 204);
    }
}
