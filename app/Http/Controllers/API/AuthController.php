<?php

namespace App\Http\Controllers\API;

use Exception;
use App\Models\User;
use Tymon\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException; // Import de l'exception JWTException

class AuthController extends Controller
{
    public function __construct()
    {
        // Le middleware 'auth:api' est appliqué à toutes les méthodes sauf 'login'
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
    public function register(Request $request)
    {
        // validation des données envoyées par le formulaire
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5|confirmed',
            'phone' => 'required|integer'
        ]);

        // si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return response()->json([
                'success' =>false,
                'errors' => $validator->errors()
            ], 400);
        }

        // créer un nouvel utilisateur
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone')
        ]);

        // générer un token JWT pour l'utilisateur
        // $token = JWTAuth::fromUser($user);

        // retourner la réponse avec le token et les informations de l'utilisateur
        return response()->json([
            'success' =>true,
            'message' => 'Successfull registered',
            'user'=> $user
        ], 201);
    }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password'); // Récupération des informations d'identification de la requête
        // validation des données envoyées par le formulaire
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:5',
        ]);

        // si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return response()->json([
                'success'=>false,
                'errors' => $validator->errors()
            ], 422);
        }

        //
        if(!$token = auth()->attempt($validator->validated())){
            return response()->json(['error'=>'Unauthorized'], 401);
        }
        return $this->createTokenKey($token); // Si les informations d'identification sont correctes, on retourne un token JWT
    }
    public function createTokenKey($token){
        return response()->json([
            'success'=>true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL()*60,
            'user' => auth()->user()
        ], 200);
    }
    public function logout()
    {   
        try {
            //code...
            Auth::logout(); // Déconnexion de l'utilisateur
            return response()->json(['success' =>true, 'message' => 'Successfully logged out']); // Retour d'un message de confirmation
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(['success' =>false, 'message' => $e->getMessage()]); // Retour d'un message de confirmation
        }

    }

    public function me()
    {

        return response()->json(auth()->user()); // Renvoi des informations de l'utilisateur connecté
    }

    public function refresh()
    {
        return response()->json(['token' => auth()->refresh()]); // Renvoi d'un nouveau token JWT
    }
}

