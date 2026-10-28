<?php

use Crater\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use function Pest\Faker\faker;

test('set setting', function () {
    $key = fake()->name;

    $value = fake()->word;

    Setting::setSetting($key, $value);

    $response = Setting::getSetting($key);

    $this->assertEquals($value, $response);
});
