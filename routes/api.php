<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\LikeController;
use App\Http\Controllers\API\MealController;
use App\Http\Controllers\API\MenuController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\CommentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'api'], function($routes){
    
    
    /*--------------------- Authentification --------------------- */
    Route::post('/register', [AuthController::class, 'register']); // enregistrement d'un nouvel utilisateur
    Route::post('/login', [AuthController::class, 'login']); // connexion d'un utilisateur existant
    
    Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
    Route::post('/reset-password', [UserController::class, 'ResetPassword']);
    Route::get('/reset-password', [UserController::class, 'ResetPasswordLoad']);
    /*--------------------- Users --------------------- */
    Route::group(['middleware' => ['auth:api']], function() {
        /*..............................Password reset ....................*/

        /*--------------------- Comments --------------------- */
        Route::post('/comments', [CommentController::class, 'store']);
        Route::get('/comments', [CommentController::class, 'listComments']);
        Route::put('/comments/{id}', [CommentController::class, 'update']);
        Route::delete('/comments/{id}', [CommentController::class, 'destroy']);

        /*--------------------- Like --------------------- */
        Route::post('/likes', [LikeController::class,'likeOrDislike']);

 
        /*--------------------- Cart --------------------- */
        Route::post('/carts', [CartController::class, 'addMeal']);
        Route::delete('/cart/{id}', [CartController::class, 'removeMeal'])->name('cart.remove');
        Route::get('/carts', [CartController::class, 'index']);
        
        /*--------------------- Images --------------------- */
        Route::post('/images', [ImageController::class, 'store']);
        Route::delete('/images/{image}', [ImageController::class, 'destroy']);
        
        /*--------------------- Authentification --------------------- */
        Route::name('logout')->get('/logout', [AuthController::class, 'logout']); // déconnexion d'un utilisateur authentifié
        Route::name('refresh')->get('/refresh', [AuthController::class, 'refresh']); // rafraîchissement du token JWT
        Route::name('profil')->get('/me', [AuthController::class, 'me']); // récupération des informations de l'utilisateur authentifié
        
        /*--------------------- Menus --------------------- */
        Route::prefix('menus')->group(function () {
            Route::controller(MenuController::class)->group(function(){
                Route::post('/', 'createMenu');
                Route::put('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });
        }); 
        
        /*--------------------- Meal --------------------- */
        Route::prefix('meals')->group(function () {
            Route::controller(MealController::class)->group(function(){
                Route::post('/', 'createMeal');
                Route::put('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
                
                Route::get('/{id}/detach-menu/{menu_id}', 'detachMenu');
                Route::get('/{id}/assign-menu/{menu_id}', 'assignMenu');
                Route::get('/{id}/has-menu/{menu_id}', 'hasMenu');
            });
            
        });   
        
        /*--------------------- Roles --------------------- */
        Route::prefix('roles')->group(function () {
            Route::controller(RoleController::class)->group(function(){
                Route::get('/', 'index');
                Route::get('/{id}', 'show');
                Route::post('/', 'store');
                Route::put('/{id}', 'update');
                Route::delete('/{id}', 'destroy');
            });
        });
        
        // Route::middleware(['admin'])->group(function () {
            Route::controller(UserController::class)->group(function(){
                // Obtenir les informations de l'utilisateur actuel
                Route::get('/user', 'getAuthenticatedUser');   
                Route::get('/users/{id}', 'getUser'); 
                Route::get('/send-verify-mail/{email}', 'sendVerifyMail');
                Route::post('/users', 'createUser');
                Route::put('/users/{id}', 'updateUser');
                Route::delete('/users/{id}', 'destroyUser');
                Route::post('/profile-update', 'updateProfile');
                //
                Route::get('/users/{id}/image', 'getUserImage');
                //
                Route::get('/users/{id}/detach-role/{role_id}', 'detachRole');
                Route::get('/users/{id}/assign-role/{role_id}', 'assignRole');
                Route::get('/users/{id}/has-role/{role_id}', 'hasRole');
            });
        // });
    });
    Route::get('/comments/{id}', [CommentController::class, 'show']);
    Route::get('/{morphComment}/comments', [CommentController::class, 'index']);
    /* Users */
    Route::get('/users', [UserController::class, 'getAllUser']);
    /* Meals */
    Route::prefix('meals')->group(function () {
        Route::controller(MealController::class)->group(function(){
            Route::get('/', 'mealList');
            Route::get('/{id}', 'show');
            Route::get('/{id}/image', 'getImageMeal');
            Route::get('/{id}/menus', 'getMealMenus');
        });
    });
    /* Menu */
    Route::prefix('menus')->group(function () {
        Route::controller(MenuController::class)->group(function(){
            Route::get('/', 'MenusList');
            Route::get('/{id}', 'show');
        });
    }); 
});
