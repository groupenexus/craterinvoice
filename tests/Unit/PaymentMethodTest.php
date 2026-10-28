<?php

use Crater\Models\PaymentMethod;
use Illuminate\Support\Facades\Artisan;

test('payment method has many payment', function () {
    $method = PaymentMethod::factory()->hasPayments(5)->create();

    $this->assertCount(5, $method->payments);

    $this->assertTrue($method->payments()->exists());
});

test('payment method belongs to company', function () {
    $method = PaymentMethod::factory()->create();

    $this->assertTrue($method->company()->exists());
});
