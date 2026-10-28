<?php

use Crater\Models\ExpenseCategory;
use Illuminate\Support\Facades\Artisan;

test('expense category has many expenses', function () {
    $category = ExpenseCategory::factory()->hasExpenses(5)->create();

    $this->assertCount(5, $category->expenses);
    $this->assertTrue($category->expenses()->exists());
});
