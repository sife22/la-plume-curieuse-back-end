<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use Auth;
use DB;
use File;
use Illuminate\Http\Request;
use Str;

class PostController extends Controller
{
    public function index(){
        $posts = Post::with('categories')->orderBy('created_at', 'DESC')->paginate(1);
        return response()->json(['posts'=>$posts], 200);
    }

    public function getPostsAuthor(){
        $user = Auth::user();
        $posts = Post::where('author_id', $user->id)->get();
        return response()->json(['posts'=>$posts], 200);
    }

    public function edit($slug){
        $post = Post::with('categories')->where('slug', $slug)->firstOrFail();
        return response()->json(['post'=>$post], 200);
    }

    public function update(Request $request, $slug){
        $request->validate([
            'title'=>'max:200',
            'summary'=>'max: 200',
        ],[
            'title.max'=>"Le nom est très long",
            'summary.required'=>"La description est requise",
        ]);

        $post = Post::with('categories')->where('slug', $slug)->firstOrFail();

        if(Category::find($request['categoryId'])){
            DB::table('category_post')
            ->where('post_id', $post->id)
            ->update(['category_id' => $request['categoryId']]);
        }

        if (!empty($request['title'])) {
            $post->title = $request['title'];
            $post->slug = Str::slug($request['title'].'-'.$post->id);
        }    

        if (!empty($request['summary'])) {
            $post->summary = $request['summary'];
        } 
        if (!empty($request['content'])) {
            $post->content = $request['content'];
        } 

        if ($request->hasFile('picture')) {
            File::delete(public_path('/uploads/posts/picture/' . $post->picture));
            $extension_image = $request['picture']->getClientOriginalExtension();
            $name_picture = microtime(true) . '.' . $extension_image;
            $chemin = 'uploads/posts/picture';
            $request['picture']->move($chemin, $name_picture);
            $post->picture = $name_picture;
        }

        $post->save();
        return response()->json(['message'=>"Vous avez modifier l'article avec succès", 'post'=>$post], 200);
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

    public function delete($slug){
        $post = Post::where('slug', $slug)->firstOrFail();
        File::delete(public_path('/uploads/posts/picture/' . $post->picture));
        $post->delete();
        return response()->json(['message'=>"Vous avez supprimé l'article avec succès", 'post'=>$post], 200);
    }
}
