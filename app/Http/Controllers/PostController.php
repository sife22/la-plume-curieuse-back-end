<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Auth;
use DB;
use Illuminate\Http\Request;
use Str;

class PostController extends Controller
{
    public function index(){
        $posts = Post::with('categories')->orderBy('created_at', 'DESC')->get();
        return response()->json(['posts'=>$posts], 200);
    }

    public function show($slug){
        $post = Post::with('categories')->where('slug', $slug)->firstOrFail();
        return response()->json(['post'=>$post], 200);
    }

    public function addView($slug){
        $post = Post::where('slug', $slug)->firstOrFail();
        $post->views += 1;
        $post->save();
        return response()->json(['message'=>'Success', 'post'=>$post], 200);
    }

    public function store(Request $request){
        $request->validate([
            'title'=>'required|max:200',
            'summary'=>'required|max: 200',
            'content'=>'required',
            'picture'=>'required',
            // Il faut vérifier l'existance de la catégorie avec cet id
            'categoryId'=>'required',
        ],[
            'title.required'=>"Le nom est requis",
            'title.max'=>"Le nom est très long",
            'summary.required'=>"La description est requise",
            'summary.max'=>"La description est très longue",
            'content.required'=>"Le contenu est requis",
            'content.max'=>"Le contenu est très long",
            'picture.required'=>"L'image est requis",
            'categoryId.required'=>"La catégorie est requise",
        ]);

        if(!Category::find($request['categoryId'])){
            return response()->json(['message'=>"La catégorie que vous avez saisi n'existe pas"], 422);
        }

        $new_post = new Post();
        $new_post->title = $request['title'];
        $new_post->summary = $request['summary'];
        $new_post->content = $request['content'];
        $new_post->author_id = Auth::user()->id;
        if ($request->hasFile('picture')) {
            $extension_image = $request['picture']->getClientOriginalExtension();
            $name_picture = microtime(true) . '.' . $extension_image;
            $chemin = 'uploads/posts/picture';
            $request['picture']->move($chemin, $name_picture);
            $new_post->picture = $name_picture;
        }
        $new_post->save();
        $new_post->slug = Str::slug($request['title'].'-'.$new_post->id);
        $new_post->save();

        DB::table('category_post')->insert([
            'post_id' => $new_post->id,
            'category_id' => $request['categoryId'],
        ]);
        return response()->json(['message'=>'Vous avez ajouté un nouveau article avec succès', 'post'=>$new_post], 200);
    }
}
