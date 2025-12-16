<?php

namespace App\Http\Controllers;

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

    public function sendMessage(Request $request, CompanySettings $companySettings)
    {
        $validated = $request->validate([
            'first_name' => 'required|max:24',
            'last_name' => 'required|max:24',
            'phone' => 'max:24',
            'email' => 'required|email',
            'message' => 'required|max:2048',
        ]);
        $contact = Contact::create($validated);

        Mail::to($companySettings->email)->send(new UserContactSent($contact));

    }
}
