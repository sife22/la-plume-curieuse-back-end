<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Str;

class CategoryController extends Controller
{
    public function index(){
        $categories = Category::all(['id', 'name', 'slug', 'description']);
        return response()->json(['categories'=>$categories], 200);
    }

    public function posts($slugCategory)
    {
    $category = Category::where('slug', $slugCategory)->first(['id', 'name', 'description', 'slug']);

    if (!$category) {
        return response()->json(['message' => 'Catégorie non trouvée'], 404);
    }
    
    $posts = $category->posts()->paginate(1);
    return response()->json(['posts' => $posts], 200);
    }

    public function show(Request $request){
        $category = Category::all(['id', 'name', 'description', 'slug'])->where('slug', $request['slug'])->firstOrFail();
        return response()->json(['category'=>$category], 200);
    }
    
    public function store(Request $request){
        
        
        $request->validate([
            'name'=>'required|max:30|unique:category,name',
            'description'=>'required|max:200'
        ],[
            'name.required'=>"Le nom est requis",
            'name.max'=>"Le nom est très long",
            'name.unique'=>"Cette catégorie existe déjà",
            'description.required'=>"La description est requise",
            'description.max'=>"La description est très longue",
        ]);

         // On doit vérifier si le slug est déjà existant, déjà fait par la fonction validate au dessus
        //  if(Category::where('slug', Str::slug($request['name']))->first()){
        //     return response()->json(['message'=>'Cette catégorie est déjà existante',], 400);
        // };

       
        
        $new_category = new Category();
        $new_category->name = $request['name'];
        $new_category->slug = Str::slug($request['name']);
        $new_category->description = $request['description'];
        $new_category->save();
        return response()->json(['message'=>'Vous avez crée la nouvelle catégorie avec succès', 'category'=>$new_category], 200);
    }

    public function update(Request $request, $slug, $id){
        $request->validate([
            'name'=>'required|max:30',
            'description'=>'required|max: 200'
        ],[
            'name.required'=>"Le nom est requis",
            'name.max'=>"Le nom est très long",
            'description.required'=>"La description est requise",
            'description.max'=>"La description est très longue",
        ]);

        $category = Category::findOrFail($id);
        $category->name = $request['name'];
        $category->description = $request['description'];
        $category->slug = Str::slug($request['name']);
        $category->save();

        return response()->json(['message'=>'Vous avez modifié la catégorie avec succès', 'category'=>$category], 200);
    }

    public function delete($slug, $id){
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message'=>'Vous avez supprimé cette catégorie avec succès', 'category'=>$category], 200);
    }
}
