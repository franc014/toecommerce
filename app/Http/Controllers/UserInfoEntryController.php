<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserInfoEntryRequest;
use Inertia\Inertia;

class UserInfoEntryController extends Controller
{
    public function store(StoreUserInfoEntryRequest $request)
    {

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

        return Inertia::flash('success', __('storefront.user_info_store_success'))
            ->back();
    }

    public function update(StoreUserInfoEntryRequest $request, $id)
    {

        auth()->user()->userInfoEntries()->where('id', $id)->update([
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
        ]);

        return Inertia::flash('success', __('storefront.user_info_store_success'))
            ->back();
    }

    public function useBillingAsShipping()
    {
        $billingInfo = auth()->user()->mainBillingInfoEntry();

        auth()->user()->userInfoEntries()->create([
            'first_name' => $billingInfo->first_name,
            'last_name' => $billingInfo->last_name,
            'type' => 'shipping',
            'country' => $billingInfo->country,
            'state' => $billingInfo->state,
            'city' => $billingInfo->city,
            'address' => $billingInfo->address,
            'phone' => $billingInfo->phone,
            'zipcode' => $billingInfo->zipcode,
            'email' => $billingInfo->email,
            'is_main' => true,
        ]);

        return Inertia::flash('success', __('storefront.user_info_store_success'))
            ->back();
    }
}
