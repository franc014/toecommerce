<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendContactRequest;
use App\Mail\UserContactSent;
use App\Models\Contact;
use App\Settings\CompanySettings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

class ContactPageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function index(CompanySettings $companySettings)
    {

        $companyInformation = [
            'phone' => $companySettings->phone,
            'email' => $companySettings->email,
            'address' => $companySettings->address,
            'whatsapp' => $companySettings->whatsapp,
            'socialMedia' => $companySettings->socialMedia,
            'workingDays' => $companySettings->workingDays

        ];

        return Inertia::render('Contact', [
            'companyInformation' => $companyInformation
        ]);
    }

    public function sendMessage(SendContactRequest $request, CompanySettings $companySettings)
    {

        $contact = Contact::create($request->validated());

        Mail::to($companySettings->email)->send(new UserContactSent($contact));

    }
}
