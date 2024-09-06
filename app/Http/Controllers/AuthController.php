<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request){

        $request->validate([
            'email'=>'required|email|max:255',
            'password'=>'required'
        ], [
            'email.required'=>"L'email est requis",
            'email.email'=>"Ex : johndoe@gmail.com",
            'email.max'=>"L'email que vous avez entré est trés long",
            'password.required'=>"Le mot de passe est requis",
        ]);

        $user = User::where('email', $request['email'])->first();

        if(!$user || !Hash::check($request['password'], $user->password)){
            return response()->json(["message"=>"L'email ou le mot de passe est incorrect"], 401);
        }

        $token = $user->createToken('ACCESS_TOKEN')->plainTextToken;

        return response()->json([
            'message'=>'Vous êtes connecté', 
            'user' => [
                'name' => $user->name,
                'username' => $user->username,
                'phone' => $user->phone,
                'email' => $user->email,
                'picture' => $user->picture,
            ],
            'access_token'=>$token], 201);
        
    }

    public function logout(Request $request){

        $request->user()->currentAccessToken()->delete();
        return response()->json(['message'=>'Vous êtes déconnecté'], 200);

    }
}
