<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = "post";

    // Pour récupérer toutes les catégories du post.
    public function categories(){
        return $this->belongsToMany(Category::class, 'category_post');
    }

    // Pour récupérer tous les commentaires du post. 
    public function comments(){
        return $this->hasMany(Comment::class);
    }

}
