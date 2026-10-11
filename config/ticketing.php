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

    /*
    |--------------------------------------------------------------------------
    | Slip Verification (EasySlip API)
    |--------------------------------------------------------------------------
    |
    | เปิดใช้งานระบบตรวจสอบสลิปโอนเงินอัตโนมัติผ่าน EasySlip API
    | หากปิดระบบจะ fallback เป็นการตรวจสอบด้วยเจ้าหน้าที่ (manual)
    |
    */
    'slip_verify_enabled' => env('SLIP_VERIFY_ENABLED', false),
    'slip_verify_api_key' => env('SLIP_VERIFY_API_KEY', ''),
];
