<?php

namespace App\Http\Controllers\API;

use Validator;
use Carbon\Carbon;
use App\Models\Like;
use App\Models\Role;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\API\ImageController;

class UserController extends Controller
{
    /**
    * appId: c79a0f048e2f12bc
    *   key: 2e5d409f733666ab2d2c390a17d7437a
    *   Configuration file saved. Run laravel-echo-server start to run server.
     * 
     */
    // public function __construct(){
    //     $this->middleware('admin')->except('getUser', 'updateUser', 'sendVerifyMail', 'verifyEmail','forgotPassword', 'ResetPasswordLoad', 'ResetPassword');
    // }

    public function getAllUser()
    {
        // récupérer tous les utilisateurs et les retourner en réponse
        $users = User::all();
        foreach($users as $user){
            $roles = $user->roles()->orderBy('name')->get();
            $image = $image = $user->image;
            $ItemLiked = Like::where('user_id', $user->id)->get();
            $ItemCommented = Comment::where('user_id', $user->id)->get();
            $user['roles'] = $roles;
            $user['image'] = $image;
            $user['liked'] = $ItemLiked;
            $user['commented'] = $ItemCommented;
        }
        return response()->json($users);
    }

    public function createUser(Request $request)
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
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // créer un nouvel utilisateur
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // retourner la réponse avec les informations de l'utilisateur
        return response()->json($user);
    }

    public function getUser($id) 
    {
        // rechercher l'utilisateur correspondant à l'identifiant
        $userExist = User::where('id', $id)->exists();
        if($userExist){
            $user = User::find($id);
            $roles = $user->roles()->orderBy('name')->get();
            $image = $image = $user->image;
            $user['roles'] = $roles;
            $user['image'] = $image;
            // retourner la réponse avec les informations de l'utilisateur
            return response()->json($user);
        }
        return response()->json(['error' => 'Utilisateur non trouvé.'], 404);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function updateUser(Request $request, $id)
    {
        //
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $userToUpdate = User::find($id);
            if (!$userToUpdate) {
                return response()->json(['message' => 'Utilisateur non trouvé'], 404);
            }
            if ($userToUpdate->id !== $user->id) {
                return response()->json(['message' => 'Action non autorisée'], 401);
            }
            if($userToUpdate->email !== $request->email){
                $userToUpdate->is_verified = false;
            }
            $userToUpdate->update($request->all());
            return response()->json([
                'message' => 'Utilisateur mis à jour avec succès',
                'user' => $userToUpdate
            ], 200);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token invalide'], 401);
        }
    }
    public function sendVerifyMail($email){
        if (auth()->user()) {
            $user = User::where('email', $email)->get();

            if (count($user) > 0) {
                $random = Str::random(40);
                $domain = URL::to('/');
                $url = $domain . '/verify-email/'. $random;

                $data['url'] = $url;
                $data['email'] = $email;
                $data['title'] = "Email Verification";
                $data['body'] = 'Please click the link below to verify your email';

                Mail::send('verifyMail', ['data' => $data], function ($message) use ($data) {
                    $message->from(config('mail.from.address'), config('mail.from.name'));
                    $message->to($data['email']);
                    $message->subject($data['title']);
                });

                $user = User::find($user[0]['id']);
                $user->remember_token = $random;
                $user->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Mail successfully sent'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found'
                ], 401);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 402);
        }
    }
    public function verifyEmail($token){
        $user = User::where('remember_token', $token)->first();

        if ($user) {
            $dateTime = Carbon::now()->format('Y-m-d H:i:s');
            $user->email_verified_at = $dateTime;
            $user->is_verified = true;
            $user->remember_token = null;
            $user->save();

            return view('emailVerified');
        } else {
            return view('emailNotVerified');
        }
    }
    public function getAuthenticatedUser()
    {
        // Récupération de l'utilisateur authentifié
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'Utilisateur non trouvé'], 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['error' => 'Token expiré'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['error' => 'Token invalide'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['error' => 'Token absent'], $e->getStatusCode());
        }

        // Retour d'une réponse JSON avec les informations de l'utilisateur
        return response()->json(compact('user'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyUser(Request $request, $id)
    {
        //
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $userToDelete = User::find($id);
            if (!$userToDelete) {
                return response()->json(['message' => 'Utilisateur non trouvé'], 404);
            }
            if ($userToDelete->id !== $user->id) {
                return response()->json(['message' => 'Action non autorisée'], 401);
            }
            $imageRepository = new ImageController();
            $imageRepository->destroy($userToDelete->image);
            $userToDelete->delete();
            return response()->json(['message' => 'Utilisateur supprimé avec succès'], 200);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Token invalide'], 401);
        }

    }

    //Forgot passwort api method 
    public function forgotPassword(Request $request){
        try{
            $user = User::where('email', $request->email)->get();
            $id = $user[0]->id;
            // dd($user);
            // return response()->json(count($user));
            if(count($user) > 0){
                $random = Str::random(40);
                $url = 'http://127.0.0.1:8081/password/new/'. $random;
    
                $data['url'] = $url;
                $data['email'] = $request->email;
                $data['title'] = "Email Verification";
                $data['body'] = 'Please click the link below to reset password';
    
                Mail::send('forgotPasswordMail', ['data' => $data], function ($message) use ($data) {
                    $message->from(config('mail.from.address'), config('mail.from.name'));
                    $message->to($data['email']);
                    $message->subject($data['title']);
                });
                
                $datetime = Carbon::now()->format('Y-m-d H-i-s');
                PasswordReset::updateOrCreate(
                    ['email'=> $request->email],
                    [
                        'token'=> $random,
                        'created_at'=> $datetime
                    ]
                );
                return response()->json(['success'=>true,  'message'=>'Please check your email to reset your password.']);
            }else{
                return response()->json(['success'=>false,  'message'=>'User not found'], 401);
            }
        }catch(\Exception $e){
            return response()->json(['success'=>false,  'message'=>$e->getMessage()], 403);
        }
    }
    

    //reset password view
    public function ResetPasswordLoad(Request $request){
        $resetData = PasswordReset::where('token', $request->token)->get();

        if(isset($request->token) && count($resetData) > 0){
            $email = $resetData[0]->email;
            $user = User::where('email', $email)->first();
            return response()->json($user, 200);
        }else{
            return response()->json(['Something wrong'], 404);
        }
    }
    
    //Password reset functionnalities
    public function ResetPassword(Request $request){
        // validation des données envoyées par le formulaire
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:5|confirmed',
        ]);

        // si la validation échoue, retourner les erreurs
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $user = User::find($request->id);
        $user->password = Hash::make($request->password);
        $user->save();
        // return response()->json($user);
        return response()->json(['success'=>true , 'user'=>$user], 200);
    }
    // public function ResetPassword(Request $request){
    //     // validation des données envoyées par le formulaire
    //     $validator = Validator::make($request->all(), [
    //         'password' => 'required|string|min:5|confirmed',
    //         // 'token' => 'required|string'
    //     ]);
    
    //     // si la validation échoue, retourner les erreurs
    //     if ($validator->fails()) {
    //         return response()->json(['errors' => $validator->errors()], 400);
    //     }
    
    //     // Find the password reset record based on the token
    //     $resetData = PasswordReset::where('token', $request->token)->first();
    
    //     // If the reset record is not found, return an error response
    //     if(!$resetData){
    //         return response()->json(['message' => 'Invalid token'], 404);
    //     }
    
    //     // Find the user based on the email address
    //     $user = User::where('email', $resetData->email)->first();
    
    //     // If the user is not found, return an error response
    //     if(!$user){
    //         return response()->json(['message' => 'User not found'], 404);
    //     }
    
    //     // Update the user's password and delete the password reset record
    //     $user->password = Hash::make($request->password);
    //     $user->save();
    //     $resetData->delete();
    
    //     // Return a success response
    //     return response()->json(['message' => 'Password reset successful'], 200);
    // }
    
/*............................................................................................ */
    public function assignRole($id, $Ids)
    {
        $Ids = json_decode($Ids, true);
        $userExist = User::where('id', $id)->exists();
        if($userExist){
            $user = User::find($id);
            // On utilise la méthode attach() pour créer une entrée dans la table pivot "role_user" entre l'utilisateur et le rôle
            $user->roles()->attach($Ids);
            // $user->roles()->attach([1, 2, 3]);
            // On retourne une réponse JSON avec un message de succès
            return response()->json(['message' => 'Le rôle a été assigné avec succès']);
        }
        return response()->json(['message' => 'Can assign role to user, User not found']);
    }
    // Méthode pour désassigner un rôle à un utilisateur
    public function detachRole(int $id, $Ids)
    {
        $Ids = json_decode($Ids, true);
        $userExist = User::where('id', $id)->exists();
        if($userExist){
            $user = User::find($id);
            // On utilise la méthode detach() pour supprimer l'entrée correspondante dans la table pivot "role_user"
            $user->roles()->detach($Ids);
            // On retourne une réponse JSON avec un message de succès
            return response()->json(['message' => 'Le rôle a été désassigné avec succès']);
        }
        return response()->json(['message' => 'Can dettach role to user, User not found']);

    }
    // Méthode pour vérifier si un utilisateur a un rôle donné
    public function hasRole($id, $role_id)
    {
        // On récupère l'utilisateur
        $user = User::find($id);
        // On utilise la méthode contains() pour vérifier si l'utilisateur a le rôle spécifié
        $hasRole = $user->roles->contains($role_id);

        // On retourne une réponse JSON avec un booléen indiquant si l'utilisateur a le rôle spécifié ou non
        return response()->json(['hasRole' => $hasRole]);
    }
}
