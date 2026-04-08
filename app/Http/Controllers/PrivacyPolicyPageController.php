<?php

namespace App\Http\Controllers;

use App\CMS\RichTextTransformable;

class PrivacyPolicyPageController extends PageController
{
    public function __construct()
    {
        parent::__construct(
            componentView: 'PrivacyPolicy',
            slug: 'politica-de-privacidad',
            transformables: [
                new RichTextTransformable,
            ],
            extendedData: []
        );
    }
}
