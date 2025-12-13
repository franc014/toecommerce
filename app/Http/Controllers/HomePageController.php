<?php

namespace App\Http\Controllers;

use App\CMS\CollectionsTransformable;
use App\CMS\FeaturedProductTransformable;
use App\CMS\FeatureTransformable;
use App\CMS\ImageTransformable;
use App\CMS\ProductsTransformable;

class HomePageController extends PageController
{
    protected $slug = 'home';
    protected $transformables = [];
    protected $view = 'Home';

    public function __construct()
    {
        $this->transformables =
             [
             new ImageTransformable,
             new ProductsTransformable,
             new FeaturedProductTransformable,
             new CollectionsTransformable,
             new FeatureTransformable
            ];

    }
}
