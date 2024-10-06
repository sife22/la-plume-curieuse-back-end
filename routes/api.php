<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PostController;
use App\Models\Newsletter;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// On a utilisé cette route pour crée les utilisateurs.
Route::post('/register', function(Request $request){
    $new_user = new User();
    $new_user->name = $request['name'];
    $new_user->username = $request['username'];
    $new_user->phone = $request['phone'];
    $new_user->role = 'admin';
    if ($request->hasFile('picture')) {
        $extension_image = $request['picture']->getClientOriginalExtension();
        $name_picture = microtime(true) . '.' . $extension_image;
        $chemin = 'uploads/users/picture';
        $request['picture']->move($chemin, $name_picture);
        $new_user->picture = $name_picture;
    }
    $new_user->email = $request['email'];
    $new_user->password = Hash::make($request['password']);
    $new_user->created_at = Carbon::now();
    $new_user->save();
    return response()->json(['message'=>'Votre nouvel compte a été bien crée'], 201);
});

// On définit les routes d'authentification.
Route::post('/login', [AuthController::class, 'login']);
Route::delete('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// On définit les routes de newsletter.
Route::get('/newsletter', [NewsletterController::class, 'index'])->middleware('auth:sanctum');
Route::post('/newsletter', [NewsletterController::class, 'subscribe']);

// On définit les routes de catégorie.
Route::get('/get-categories', [CategoryController::class, 'index']);
Route::get('/get-category', [CategoryController::class, 'show']);
Route::get('/get-posts/{slugCategory}', [CategoryController::class, 'posts']);
Route::post('/add-category', [CategoryController::class, 'store'])->middleware('auth:sanctum');
Route::put('/update-category/{slug}-{id}', [CategoryController::class, 'update'])->where(['slug' => '[a-z0-9\-]+', 'id' => '[0-9]+']);
Route::delete('/delete-category/{slug}-{id}', [CategoryController::class, 'delete'])->where(['slug' => '[a-z0-9\-]+', 'id' => '[0-9]+']);

// On définit les routes de post.
Route::get('/get-posts', [PostController::class, 'index']);
Route::get('/get-posts-author', [PostController::class, 'getPostsAuthor'])->middleware('auth:sanctum');
Route::get('/edit-post/{slug}', [PostController::class, 'edit'])->middleware('auth:sanctum');
Route::post('/update-post/{slug}', [PostController::class, 'update'])->middleware('auth:sanctum');
Route::get('/get-post/{slug}', [PostController::class, 'show']);
Route::post('/add-post', [PostController::class, 'store'])->middleware('auth:sanctum');
Route::put('/add-view/{slug}', [PostController::class, 'addView']);
Route::delete('/delete-post/{slug}', [PostController::class, 'delete']);

// On définit les routes de commentaire.
Route::get('/get-comments/{slug}', [CommentController::class, 'index']);
Route::post('/add-comment/{slug}', [CommentController::class, 'store']);

// On définit les routes de tableau de bord admin.
Route::get('/get-statistic', [AuthController::class, 'index'])->middleware('auth:sanctum');



