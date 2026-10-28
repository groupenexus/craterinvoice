<?php

use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\{getJson};

beforeEach(function () {

    $user = User::first();
    $this->withHeaders([
        'company' => $user->companies()->first()->id,
    ]);

    Sanctum::actingAs(
        $user,
        ['*']
    );
});

test('next number', function () {
    $key = 'invoice';

    $response = getJson('api/v1/next-number?key='.$key);

    $response->assertStatus(200)->assertJson([
        'nextNumber' => 'INV-000001',
    ]);

    $key = 'estimate';

    $response = getJson('api/v1/next-number?key='.$key);

    $response->assertStatus(200)->assertJson([
        'nextNumber' => 'EST-000001',
    ]);

    $key = 'payment';

    $response = getJson('api/v1/next-number?key='.$key);

    $response->assertStatus(200)->assertJson([
        'nextNumber' => 'PAY-000001',
    ]);
});
