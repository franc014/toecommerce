<?php

use App\Mail\UserContactSent;
use App\Models\Contact;
use App\Settings\CompanySettings;
use Illuminate\Support\Facades\Mail;

test('user can send contact form', function () {

    config()->set('honeypot.enabled', false);
    $companySettings = app(CompanySettings::class);
    $companySettings->email = 'customer@example.com';
    $companySettings->save();

    Mail::fake();

    $this->post(route('storefront.send-message'), [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '1234567890',
        'email' => 'customer@example.com',
        'message' => 'This is a test message',
    ])
        ->assertRedirect();

    $this->assertDatabaseHas('contacts', [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'phone' => '1234567890',
        'email' => 'customer@example.com',
        'message' => 'This is a test message',
    ]);

    $contact = Contact::first();
    $userContactSentMailable = new UserContactSent($contact);

    Mail::assertSent(function (UserContactSent $mail) use ($companySettings, $contact) {
        return $contact->id === $mail->contact->id
         && $mail->hasTo($companySettings->email)
         && $mail->hasSubject('Nuevo mensaje de contacto')
         && $mail->hasFrom(env('MAIL_FROM_ADDRESS'));
    });

    $userContactSentMailable->assertSeeInHtml('Nuevo mensaje de contacto.');

});
