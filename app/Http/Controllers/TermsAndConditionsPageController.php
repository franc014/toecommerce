<?php

namespace App\Http\Controllers;

use App\CMS\RichTextTransformable;

class TermsAndConditionsPageController extends PageController
{
    protected $slug = 'terminos-y-condiciones';

    protected $transformables = [];

    protected $view = 'TermsAndConditions';

    public function __construct()
    {
        $this->transformables =
            [
                new RichTextTransformable,
            ];
    }
}
