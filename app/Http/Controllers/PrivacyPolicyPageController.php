<?php

namespace App\Http\Controllers;

use App\CMS\RichTextTransformable;

class PrivacyPolicyPageController extends PageController
{
    public function __construct()
    {
        $this->slug = 'politica-de-privacidad';
        $this->view = 'PrivacyPolicy';
        $this->transformables =
            [
                new RichTextTransformable,
            ];
    }
}
