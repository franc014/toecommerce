<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendContactRequest;
use App\Mail\UserContactSent;
use App\Models\Contact;
use App\Settings\CompanySettings;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Spatie\Honeypot\Honeypot;

class ContactPageController extends PageController
{
    public function __construct(Honeypot $honeypot)
    {
        $this->slug = 'contact';
        $this->view = 'Contact';
        $this->extendedData = ['honeypot' => $honeypot];
    }

    public function sendMessage(SendContactRequest $request, CompanySettings $companySettings)
    {

        $contact = Contact::create($request->validated());
        Mail::to($companySettings->email)->send(new UserContactSent($contact));

        return Inertia::flash('success', __('storefront.contact_success'))->back();

    }
}
