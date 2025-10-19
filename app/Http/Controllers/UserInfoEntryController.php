<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserInfoEntryController extends Controller
{
    public function store(Request $request)
    {

        $request->validate([
            'type' => 'required|in:billing,shipping',
            'email' => 'required|email',
            'first_name' => 'required|min:2|max:16',
            'last_name' => 'required|min:2|max:16',
            'country' => 'max:24',
            'city' => 'required|min:2|max:24',
            'address' => 'required|min:2|max:128',
            'state' => 'max:24',
            'phone' => 'max:24',
            'zipcode' => 'required|min:4|max:6',
        ]);

        auth()->user()->userInfoEntries()->create([
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
            'is_main' => true,
        ]);

        return redirect()->intended(route('storefront.checkout', absolute: false));
    }
}
