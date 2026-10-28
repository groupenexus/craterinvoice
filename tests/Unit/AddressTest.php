<?php

use Crater\Models\Address;
use Illuminate\Support\Facades\Artisan;

test('an address belongs to user', function () {
    $address = Address::factory()->forUser()->create();

    $this->assertTrue($address->user->exists());
});

test('an address belongs to country', function () {
    $address = Address::factory()->create();

    $this->assertTrue($address->country->exists());
});

test('an address belongs to customer', function () {
    $address = Address::factory()->forCustomer()->create();

    $this->assertTrue($address->customer()->exists());
});
