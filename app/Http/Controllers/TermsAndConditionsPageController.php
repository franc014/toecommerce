<?php

namespace App\Http\Controllers;

use App\CMS\RichTextTransformable;

class TermsAndConditionsPageController extends PageController
{
    public function __construct()
    {
        parent::__construct(
            componentView: 'TermsAndConditions',
            slug: 'terminos-y-condiciones',
            transformables: [
                new RichTextTransformable,
            ],
            extendedData: []
        );
    }
}
