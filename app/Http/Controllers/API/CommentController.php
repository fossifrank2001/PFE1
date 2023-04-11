<?php

namespace App\Http\Controllers\API;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($morphComment)
    {   
        $types = ['App\\Models\\Meal', 'App\\Models\\Event'];
        $morphCommentJson = json_decode($morphComment);
        $commentableType = $types[$morphCommentJson[0]];
        $commentableId = $morphCommentJson[1];
        // Récupérez tous les commentaires pour l'objet commentable en utilisant l'ID fourni en paramètre, sans se soucier du type
        $comments = Comment::where('commentable_id', $commentableId)
        ->where('commentable_type', [$commentableType, null])
        ->with('user')
        ->get();

        return response()->json($comments);
    }

    public function listComments(){
        // Vérifiez si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $comments = Comment::all();
        return response()->json($comments);
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
        // Vérifiez si l'utilisateur est authentifié
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Récupérez l'objet commentable en utilisant le type et l'ID fournis dans la requête
        $commentableType = $request->commentable_type;
        $commentableId = $request->commentable_id;
        $commentable = $commentableType::findOrFail($commentableId);

        // Créez un nouveau commentaire en utilisant les données de la requête et l'utilisateur connecté
        $comment = new Comment([
            'content' => $request->input('content'),
            'user_id' => Auth::id(),
            'commentable_id' => $commentableId,
            'commentable_type' => $commentableType
        ]);

        // Enregistrez le commentaire pour l'objet commentable
        $commentable->comments()->save($comment);

        return response()->json($comment);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Récupérez l'utilisateur connecté
        $user = auth()->user();

        // Récupérez le commentaire en utilisant l'ID fourni
        $comment = Comment::with('user')->find($id);

        // Vérifiez si le commentaire existe
        if (!$comment) {
            return response()->json(['message' => 'Comment not found'], 404);
        }

        // Vérifiez si l'utilisateur est autorisé à accéder au commentaire
        if ($comment->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($comment);
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
        //
        $comment = Comment::findOrFail($id);

        if (Auth::id() !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->update([
            'content' => $request->input('content'),
        ]);

        return response()->json($comment);
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
        $comment = Comment::findOrFail($id);

        if (Auth::id() !== $comment->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['status' => true], 204);
    }
}
