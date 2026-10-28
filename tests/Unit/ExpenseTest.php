<?php

use Crater\Models\Expense;
use Illuminate\Support\Facades\Artisan;

test('expense belongs to category', function () {
    $expense = Expense::factory()->forCategory()->create();

    $this->assertTrue($expense->category()->exists());
});

test('expense belongs to customer', function () {
    $expense = Expense::factory()->forCustomer()->create();

    $this->assertTrue($expense->customer()->exists());
});

test('expense belongs to company', function () {
    $expense = Expense::factory()->forCompany()->create();

    $this->assertTrue($expense->company()->exists());
});
