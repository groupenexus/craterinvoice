<?php

use Crater\Models\Address;
use Crater\Models\Country;
use Illuminate\Support\Facades\Artisan;

test('country has many addresses', function () {
    $country = Country::find(1);

    $address = Address::factory()->count(5)->create([
        'country_id' => $country->id,
    ]);

    $this->assertTrue($country->address()->exists());
});
