<?php

use Crater\Models\User;
use Illuminate\Support\Facades\Artisan;

test('user belongs to currency', function () {
    $user = User::factory()->create();

    $this->assertTrue($user->currency()->exists());
});

test('user belongs to many companies', function () {
    $user = User::factory()->hasCompanies(5)->create();

    $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $user->companies);
});
