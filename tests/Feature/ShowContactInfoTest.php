<?php

use App\Settings\CompanySettings;
use Inertia\Testing\AssertableInertia as Assert;

test('can show company information', function () {

    $companySettings = app(CompanySettings::class);
    $companyInformation = [
        'name' => 'Acme',
        'email' => 'acmeinc@ez',
        'phone' => '1234567890',
        'address' => '123 Main St',
        'whatsapp' => '1234567890',
        'socialMedia' => [
            'facebook' => 'https://www.facebook.com',
            'twitter' => 'https://www.twitter.com',
            'instagram' => 'https://www.instagram.com',
        ],
        'workingDays' => [
            'monday' => '7:00 - 16:00',
            'tuesday' => '7:00 - 16:00',
            'wednesday' => '7:00 - 16:00',
            'thursday' => '7:00 - 16:00',
        ],
    ];

    foreach ($companyInformation as $key => $value) {
        $companySettings->{$key} = $value;
    }

    $companySettings->save();

    $this->get(route('storefront.contact'))->assertInertia(
        fn (Assert $page) => $page
            ->has(
                'companyInformation',
                function (Assert $page) use ($companyInformation) {
                    $page->where('phone', $companyInformation['phone']);
                    $page->where('email', $companyInformation['email']);
                    $page->where('address', $companyInformation['address']);
                    $page->where('whatsapp', $companyInformation['whatsapp']);
                    $page->where('socialMedia.facebook', $companyInformation['socialMedia']['facebook']);
                    $page->where('socialMedia.twitter', $companyInformation['socialMedia']['twitter']);
                    $page->where('socialMedia.instagram', $companyInformation['socialMedia']['instagram']);
                    $page->where('workingDays.monday', $companyInformation['workingDays']['monday']);
                    $page->where('workingDays.tuesday', $companyInformation['workingDays']['tuesday']);
                    $page->where('workingDays.wednesday', $companyInformation['workingDays']['wednesday']);
                    $page->where('workingDays.thursday', $companyInformation['workingDays']['thursday']);

                }
            )
    );
});
