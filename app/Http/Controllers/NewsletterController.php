<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Validator;

class NewsletterController extends Controller
{

    public function index(){
        $emails = Newsletter::all();
        return response()->json(['emails'=>$emails], 200);
    }

    public function subscribe(Request $request){

        // on valide les données récupérées
        $request->validate([
            'email'=>'required|email|unique:newsletter,email',
        ],[
            'email.required'=>"L'email est requis !",
            'email.email'=>"Ex: contact@gmail.com",
            'email.unique'=>"Vous êtes déjà inscrit !"
        ]);

        // on enregiste l'email
        $new_email = new Newsletter();
        $new_email->email = $request['email'];
        $new_email->created_at = Carbon::now();
        $new_email->save();

        return response()->json([''=>''], 201);
    }
}
