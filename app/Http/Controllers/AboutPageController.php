<?php

namespace App\Http\Controllers;

use App\CMS\FeatureTransformable;
use App\CMS\ImageTransformable;
use App\CMS\RichTextTransformable;

class AboutPageController extends PageController
{
    public function __construct()
    {
        $this->view = 'About';
        $this->slug = 'acerca-de';
        $this->transformables =
            [
                new ImageTransformable,
                new RichTextTransformable,
                new FeatureTransformable,
            ];

    }
}
