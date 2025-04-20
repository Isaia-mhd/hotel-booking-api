<?php

namespace App\Http\Controllers;

use App\Events\NewContact;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function getAll()
    {
        $contacts = Contact::all();

        return response()->json([
            "contacts" => $contacts
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name"=> "required|string",
            "email"=> "required|email",
            "objet"=> "required|string",

        ]);

        $contact = Contact::create($request->all());

        $admins = User::where("role", "admin")->get();
        foreach($admins as $admin)
        {
            $admin->notify(new \App\Notifications\NewContact($contact));
        }

        return response()->json([
            "message" => "Message sent !"
        ], 200);
    }
}
