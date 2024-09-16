<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = "category";

    // Pour récupérer les posts d'une catégories spécifique.
    public function posts(){
        return $this->belongsToMany(Post::class, 'category_post');
    }

}
