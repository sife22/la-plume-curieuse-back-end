<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    public function index($slug) {
        $post = Post::where('slug', $slug)->firstOrFail();
        $comments = $post->comments; 
        return response()->json(['comments' => $comments], 200);
    }

    public function store(Request $request, $slug){
        $post = Post::where('slug', $slug)->firstOrFail();
        $request->validate([
            'authorName'=>'required|max:30',
            'content'=>'required|max:200'
        ],[
            'authorName.required'=>"Le nom est requis",
            'authorName.max'=>"Le nom est très long",
            'content.required'=>"Le commentaire est requis",
            'content.max'=>"Le commentaire est très long",
        ]);

        $new_comment = new Comment();
        $new_comment->author_name = $request['authorName'];
        $new_comment->content = $request['content'];
        $new_comment->post_id = $post->id;
        $new_comment->save();

        return response()->json(['message'=>'Vous avez ajouté votre commentaire avec succès', 'comment'=>$new_comment], 200);
    }
}
