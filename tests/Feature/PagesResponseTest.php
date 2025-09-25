<?php

it('gives successful response for home page', function () {
    $response = $this->get(route('storefront.home'));
    $response->assertStatus(200);
});
