<?php

namespace App\Http\Controllers\API;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;

class MenuController extends Controller
{   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function MenusList()
    {
        //
        $menus = Menu::get();
        return response()->json([
            'success'=> true,
            'message'=> 'Menus listed',
            'menus' => $menus
        ], 200);
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
        $isMenuExist = Menu::where('id', $id)->exists();
        if($isMenuExist){
            $menu = Menu::find($id);
            return response()->json([
                'success'=> true,
                'message'=> 'Menus found',
                'menu' => $menu
            ], 200);
        }else{
            return response()->json([
                'success'=> false,
                'message'=> 'No Menu found',
            ], 404);
        }
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createMenu(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:menus',
            'description' => 'required',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors());
        }
        $menu = new Menu();
        $menu->name = $request->input('name');
        $menu->description = $request->input('description');
        $menu->save();
        return response()->json([
            'message' => 'Menu succesfull created',
            'menu' => $menu
        ], 201);
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
        $isMenuExist = Menu::where('id', $id)->exists();
        if($isMenuExist){
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'description' => 'required',
            ]);
            if($validator->fails()){
                return response()->json($validator->errors());
            }
            $menu = Menu::find($id);
            $menu->name = $request->input('name');
            $menu->description = $request->input('description');
            $menu->save();
            return response()->json([
                'message' => 'Menu succesfull Updated',
                'menu' => $menu
            ], 201);
        }else{
            return response()->json([
                'success'=> false,
                'message'=> 'Faild to update',
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
        $isMenuExist = Menu::where('id', $id)->exists();
        if($isMenuExist){
            $menu = Menu::find($id);
            $menu->delete();
            return response()->json([
                'success'=> true,
                'message'=> 'Successfull deleted',
            ]);
        }else{
            return response()->json([
                'success'=> false,
                'message'=> 'No Menu found',
            ], 404);
        }
    }
}
