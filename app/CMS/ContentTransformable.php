<?php

namespace App\CMS;

interface ContentTransformable
{
    public function transform(array $item): array;
}
