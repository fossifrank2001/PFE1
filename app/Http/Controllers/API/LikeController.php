<?php

namespace App\Http\Controllers\API;

use App\Models\Like;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LikeController extends Controller
{
    public function likeOrDislike(Request $request)
    {
        // $user = $request->user();
        $user = auth()->user();
        $likableType = $request->input('likable_type');
        $likableId = $request->input('likable_id');

        $like = Like::where('user_id', $user->id)
                    ->where('likable_type', $likableType)
                    ->where('likable_id', $likableId)
                    ->first();

        if ($like) {
            if ($like->liked) {
                // User already liked the component, so dislike it
                $like->liked = false;
                $like->save();
                $response = ['status' => 'disliked'];
            } else {
                // User already disliked the component, so remove the dislike and like it
                $like->delete();
                $like = new Like;
                $like->user_id = $user->id;
                $like->likable_type = $likableType;
                $like->likable_id = $likableId;
                $like->liked = true;
                $like->save();
                $response = ['status' => 'liked'];
            }
        } else {
            // User has not interacted with the component, so like it
            $like = new Like;
            $like->user_id = $user->id;
            $like->likable_type = $likableType;
            $like->likable_id = $likableId;
            $like->liked = true;
            $like->save();
            $response = ['status' => 'liked'];
        }

        return response()->json($response);
    }
}
