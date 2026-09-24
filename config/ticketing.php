<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ticketing business rules
    |--------------------------------------------------------------------------
    |
    | Keep prices and PromptPay settings out of controllers so that online and
    | point-of-sale flows always calculate the same amount.
    |
    */
    'price_per_seat' => (int) env('TICKET_PRICE_PER_SEAT', 50),
    'qr_payment_fee' => (int) env('TICKET_QR_PAYMENT_FEE', 10),
    // Replace this development fallback in every deployed environment.
    'promptpay_target' => env('PROMPTPAY_TARGET', '0886385512'),
];
