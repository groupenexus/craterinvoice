<?php

namespace Database\Factories;

use Crater\Models\Currency;
use Crater\Models\ExchangeRateLog;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExchangeRateLogFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ExchangeRateLog::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_id' => User::first()->companies()->first()->id,
            'base_currency_id' => Currency::first()->id,
            'currency_id' => Currency::where('code', 'ZAR')->first()->id,
            'exchange_rate' => $this->faker->randomDigitNotNull
        ];
    }
}
