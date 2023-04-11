<?php

namespace App\Http\Controllers\API;

use App\Models\Meal;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\ImageController;

class MealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function mealList()
    {
        //
        $meals = Meal::orderBy('id', 'DESC')->get();
        foreach ($meals as $meal) {
            $comments = $meal->comments()->orderBy('id')->get();
            $likes = $meal->likes()->orderBy('id')->get();
            $meal['comments'] =$comments;
            $meal['likes'] =$likes;
            $menus = $meal->menus()->orderBy('id')->get();
            $image = $meal->image;
            $meal['menus'] = $menus;
            $meal['image'] = $image;
        }
        return response()->json([
            'success'=> true,
            'message'=> 'Menus listed',
            'meals' => $meals
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
        $isMealExist = Meal::where('id', $id)->exists();
        if($isMealExist){
            $meal = Meal::find($id);
            $menus = $meal->menus()->orderBy('id')->get();
            $comments = $meal->comments()->orderBy('id')->get();
            $likes = $meal->likes()->orderBy('id')->get();
            $image = $meal->image;
            $meal['menus'] = $menus;
            $meal['image'] = $image;
            $meal['comments'] = $comments;
            $meal['likes'] =$likes;
            return response()->json([
                'success'=> true,
                'message'=> 'Menus found',
                'meal' => $meal
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
    public function createMeal(Request $request)
    {
        //'name', 'price', 'description', 'status'
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:meals',
            'price' => 'required|integer',
            'description' => 'required|string',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors());
        }
        $meal = new Meal();
        $meal->name = $request->input('name');
        $meal->price = $request->input('price');
        $meal->description = $request->input('description');
        $meal->status = $request->input('status');
        $meal->checkpub = $request->input('checkpub');
        $meal->save();
        return  response()->json([
            'success' => true,
            'message' => 'Menu succesfull created',
            'meal' => $meal
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
        $isMealExist = Meal::where('id', $id)->exists();
        if($isMealExist){
            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:menus',
                'price' => 'required|integer',
                'description' => 'required|string',
                'status' => 'required|string',
            ]);
            if($validator->fails()){
                return response()->json($validator->errors());
            }
            $meal = new Meal();
            $meal->name = $request->input('name');
            $meal->price = $request->input('price');
            $meal->description = $request->input('description');
            $meal->status = $request->input('status');
            $meal->save();
            $meal->image()->delete();
            return response()->json([
                'success'=> true,
                'message' => 'Menu succesfull Updated',
                'meal' => $meal
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
        $isMealExist = Meal::where('id', $id)->exists();
        if($isMealExist){
            $meal = Meal::find($id);
            $imageRepository = new ImageController();
            $imageRepository->destroy($meal->image);
            $meal->delete();
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
/*............................................................................................ */
    // Méthode pour recuperer tous les rôles d' un utilisateur
    public function getMealMenus($id){
        $mealExist = Meal::where('id', $id)->exists();
        if($mealExist){
            $menus = Meal::find($id)->menus()->orderBy('id')->get();
            return response()->json($menus);
        }
        return response()->json(['Impossible to get meal\'s menus'], 400);
    }
    // Méthode pour recuoerer l'image d'un repas
    public function getImageMeal($id){
        // $post = Post::find(1);
        $mealExist = Meal::where('id', $id)->exists();
        if($mealExist){
            $meal = Meal::find($id);
            $image = $meal->image;
            return response()->json($image);
        }
        return response()->json(['Impossible to get meal\'s Image'], 400);
    }
    // Méthode pour assigner un rôle à un utilisateur qui a l'id
    public function assignMenu($id, $Ids)
    {
        $Ids = json_decode($Ids, true);
        $mealExist = Meal::where('id', $id)->exists();
        if($mealExist){
            $meal = Meal::find($id);
            // On utilise la méthode attach() pour créer une entrée dans la table pivot "role_user" entre l'utilisateur et le rôle
            $meal->menus()->attach($Ids);
            // $user->roles()->detach([1, 2, 3]);
            // On retourne une réponse JSON avec un message de succès
            return response()->json(['message' => 'Le menus has been successfull assigned']);
        }
        return response()->json(['message' => 'Can assign menu to a meal, Meal not found']);
    }
    // Méthode pour désassigner un rôle à un utilisateur
    public function detachMenu(int $id, $ids)
    {
        $Ids = json_decode($ids, true);
        $mealExist = Meal::where('id', $id)->exists();
        if($mealExist){
            $meal = Meal::find($id);
            // On utilise la méthode detach() pour supprimer l'entrée correspondante dans la table pivot "role_user"
            $meal->menus()->detach($ids);
            // On retourne une réponse JSON avec un message de succès
            return response()->json(['message' => 'Le rôle a été désassigné avec succès']);
        }
        return response()->json(['message' => 'Can dettach role to user, User not found']);

    }
    // Méthode pour vérifier si un utilisateur a un rôle donné
    public function hasMenu($id, $menu_id)
    {
        // On récupère l'utilisateur
        $user = User::find($id);
        // On utilise la méthode contains() pour vérifier si l'utilisateur a le rôle spécifié
        $hasMenu = $user->menus->contains($menu_id);

        // On retourne une réponse JSON avec un booléen indiquant si l'utilisateur a le rôle spécifié ou non
        return response()->json(['hasRole' => $hasMenu]);
    }
}
