<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendContactRequest;
use App\Mail\UserContactSent;
use App\Models\Contact;
use App\Settings\CompanySettings;
use Illuminate\Support\Facades\Mail;

class ContactPageController extends PageController
{

    public function __construct()
    {
        $this->slug = 'contact';
        $this->view = 'Contact';
    }

    public function sendMessage(SendContactRequest $request, CompanySettings $companySettings)
    {

        $contact = Contact::create($request->all());
        Mail::to($companySettings->email)->send(new UserContactSent($contact));

    }
}
