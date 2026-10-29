<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSiretAndVatToCustomerAddressFormats extends Migration
{
    const FORMATS = [
        'invoice_billing_address_format',
        'estimate_billing_address_format',
        'payment_from_customer_address_format',
    ];

    const LINES = '<p>{CONTACT_SIRET}</p><p>{CONTACT_VAT_NUMBER}</p>';

    /**
     * Show the customer SIRET and VAT number under the address on existing companies' PDFs.
     *
     * @return void
     */
    public function up()
    {
        DB::table('company_settings')
            ->whereIn('option', self::FORMATS)
            ->where('value', 'not like', '%{CONTACT_SIRET}%')
            ->update(['value' => DB::raw("CONCAT(value, '".self::LINES."')")]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('company_settings')
            ->whereIn('option', self::FORMATS)
            ->update(['value' => DB::raw("REPLACE(value, '".self::LINES."', '')")]);
    }
}
