<?php

namespace App\Http\Controllers;

use App\CMS\RichTextTransformable;

class PrivacyPolicyPageController extends PageController
{
    protected $slug = 'politica-de-privacidad';

    protected $transformables = [];

    protected $view = 'PrivacyPolicy';

    public function __construct()
    {
        $this->transformables =
            [
                new RichTextTransformable,
            ];
    }
}
