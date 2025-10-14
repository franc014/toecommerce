<?php

use App\Utils\PayphoneTransactionIdGenerator;
use Illuminate\Support\Str;
use Symfony\Component\Uid\Ulid;

it('generates a ulid transaction id', function () {
    $generator = new PayphoneTransactionIdGenerator;

    Str::createUlidsUsing(function () {
        return new Ulid('01HRDBNHHCKNW2AK4Z29SN82T9');
    });

    expect($generator->generate())->toBe('01HRDBNHHCKNW2AK4Z29SN82T9');
});
