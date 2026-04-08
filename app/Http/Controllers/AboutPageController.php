<?php

namespace App\Http\Controllers;

use App\CMS\FeatureTransformable;
use App\CMS\ImageTransformable;
use App\CMS\RichTextTransformable;

class AboutPageController extends PageController
{
    public function __construct()
    {
        parent::__construct(
            componentView: 'About',
            slug: 'acerca-de',
            transformables: [
                new ImageTransformable,
                new RichTextTransformable,
                new FeatureTransformable,
            ],
            extendedData: []
        );
    }
}
