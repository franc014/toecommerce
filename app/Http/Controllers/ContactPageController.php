<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendContactRequest;
use App\Mail\UserContactSent;
use App\Models\Contact;
use App\Settings\CompanySettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Spatie\Honeypot\Honeypot;

class ContactPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(CompanySettings $companySettings, Honeypot $honeypot)
    {

        $companyInformation = [
            'phone' => $companySettings->phone,
            'email' => $companySettings->email,
            'address' => $companySettings->address,
            'whatsapp' => $companySettings->whatsapp,
            'socialMedia' => $companySettings->socialMedia,
            'workingDays' => $companySettings->workingDays,

        ];

        return Inertia::render('Contact', [
            'companyInformation' => $companyInformation,
            'honeypot' => $honeypot
        ]);
    }

    public function sendMessage(SendContactRequest $request, CompanySettings $companySettings)
    {

        //$request->validated()
        $contact = Contact::create($request->all());

        Mail::to($companySettings->email)->send(new UserContactSent($contact));

    }
}
