<?php

namespace App\Http\Controllers\API;

use App\Models\Cart;
use App\Http\Controllers\Controller;
use App\Models\Meal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
        public function index()
        {
            $carts = Cart::all();
            return response()->json($carts);
        }
        
        public function addMeal(Request $request)
        {
            // Retrieve the data from the form submitted by the client
            $meal_Id = $request->meal_id;
        
            // Retrieve the logged-in user
            $user = auth()->user();
        
            // Check if this product is already in the user's cart
            // Find the cart item for this meal and user, or create a new one
            $cartItem = Cart::where('user_id', $user->id)
                ->where('meal_id', $meal_Id)
                ->firstOrNew();
        
            // Increment the quantity by 1
            $cartItem->user_id = $user->id;
            $cartItem->meal_id = $meal_Id;
            $cartItem->quantity += 1;
        
            // Save the cart item
            $cartItem->save();
        
            // Retrieve the updated cart items for the user
            $user->load('carts');
        
            // Return a JSON response to inform the client that the cart has been updated successfully
            return response()->json([
                'success' => true,
                'message' => 'Le produit a été ajouté au panier avec succès.',
                'quantity' => $user->carts->sum('quantity'),
            ]);
        }
    
        public function decrementQuantity($meal_id, $user_id)
        {
            $cart = Cart::where('meal_id', $meal_id)
                        ->where('user_id', $user_id)
                        ->first();

            if ($cart) {
                $quantity = $cart->quantity;

                if ($quantity > 1) {
                    $cart->update(['quantity' => $quantity - 1]);
                } else {
                    $cart->delete();
                }
            }
        }

        public function removeMeal($id)
        {
            $cart = Cart::where('user_id', auth()->id())
                        ->where('meal_id', $id)
                        ->first();

            if (!$cart) {
                return response()->json(['message' => 'Meal not found in cart.'], 404);
            }

            $cart->delete();

            return response()->json(['message' => 'Meal removed from cart.']);
        }

}
