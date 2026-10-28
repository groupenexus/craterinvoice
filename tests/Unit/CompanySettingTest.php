<?php

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Illuminate\Support\Facades\Artisan;
use function Pest\Faker\faker;

test('company setting belongs to company', function () {
    $setting = CompanySetting::factory()->create();

    $this->assertTrue($setting->company()->exists());
});

test('set settings', function () {
    $key = fake()->name;

    $value = fake()->word;

    $company = Company::factory()->create();

    CompanySetting::setSettings([$key => $value], $company->id);

    $response = CompanySetting::getSetting($key, $company->id);

    $this->assertEquals($value, $response);
});

test('get settings', function () {
    $key = fake()->name;

    $value = fake()->word;

    $company = Company::factory()->create();

    CompanySetting::setSettings([$key => $value], $company->id);

    $response = CompanySetting::getSettings([$key], $company->id);

    $this->assertEquals([$key => $value], $response->toArray());
});
