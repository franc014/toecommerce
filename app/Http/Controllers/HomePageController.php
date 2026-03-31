<?php

namespace App\Http\Controllers;

use App\CMS\CollectionsTransformable;
use App\CMS\FeaturedProductTransformable;
use App\CMS\FeatureTransformable;
use App\CMS\ImageTransformable;
use App\CMS\ProductsTransformable;

class HomePageController extends PageController
{
    public function __construct()
    {

        parent::__construct(
            componentView: 'Home',
            slug: 'home',
            transformables: [
                new ImageTransformable,
                new ProductsTransformable,
                new FeaturedProductTransformable,
                new CollectionsTransformable,
                new FeatureTransformable,
            ],
            extendedData: []
        );
    }
}
