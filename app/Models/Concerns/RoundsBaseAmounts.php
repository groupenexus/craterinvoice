<?php

namespace Crater\Models\Concerns;

/**
 * Base-currency amount columns (base_total, base_tax, base_amount, ...) are
 * stored as whole numbers. Round them when they are written so every database
 * engine stores the same value (MySQL rounds, SQLite keeps the decimals).
 */
trait RoundsBaseAmounts
{
    public function setAttribute($key, $value)
    {
        if ($this->isBaseAmountKey($key) && is_numeric($value)) {
            $value = (int) round($value);
        }

        return parent::setAttribute($key, $value);
    }

    private function isBaseAmountKey($key)
    {
        return is_string($key)
            && str_starts_with($key, 'base_')
            && ! str_ends_with($key, '_id');
    }
}
