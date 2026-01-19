<?php

namespace App\Http\Controllers;

use App\CMS\RichTextTransformable;

class TermsAndConditionsPageController extends PageController
{
    public function __construct()
    {
        $this->slug = 'terminos-y-condiciones';
        $this->view = 'TermsAndConditions';

        $this->transformables =
            [
                new RichTextTransformable,
            ];
    }
}
