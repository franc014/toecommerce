<?php

namespace App\Http\Controllers;

use App\Models\UserInfoEntry;
use Illuminate\Http\Request;

class UserInfoEntryController extends Controller
{
    public function store(Request $request)
    {

        UserInfoEntry::create([
            'user_id' => auth()->user()->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'type' => $request->type,
            'country' => $request->country,
            'state' => $request->state,
            'city' => $request->city,
            'address' => $request->address,
            'phone' => $request->phone,
            'zipcode' => $request->zipcode,
            'email' => $request->email,
            'is_main' => true
        ]);

        return  redirect()->intended(route('storefront.checkout', absolute: false));
    }
}
