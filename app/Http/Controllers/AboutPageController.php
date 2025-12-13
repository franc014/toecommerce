<?php

namespace App\Http\Controllers;

use App\CMS\FeatureTransformable;
use App\CMS\ImageTransformable;
use App\CMS\RichTextTransformable;

class AboutPageController extends PageController
{
    protected $slug = 'acerca-de';
    protected $transformables = [];
    protected $view = 'About';


    public function __construct()
    {
        $this->transformables =
            [
                new ImageTransformable,
                new RichTextTransformable,
                new FeatureTransformable
            ];
    }

}
