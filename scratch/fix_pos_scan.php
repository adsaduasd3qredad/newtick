<?php
$file = 'app/Http/Controllers/PosController.php';
$content = file_get_contents($file);

$search = <<<PHP
    public function verifyBooking(Request \$request)
    {
        \$ref = \$request->input('ref');
        \$booking = Booking::with('showtime.movie')
            ->where('qr_payment_ref', \$ref)
            ->orWhere('qr_ticket_ref', \$ref)
            ->orWhere('id', \$ref)
            ->first();
PHP;

$replace = <<<PHP
    public function verifyBooking(Request \$request)
    {
        \$ref = \$request->input('ref');
        \$cleanId = ltrim(\$ref, '#');
        \$booking = Booking::with('showtime.movie')
            ->where('qr_payment_ref', \$ref)
            ->orWhere('qr_ticket_ref', \$ref)
            ->orWhere('id', is_numeric(\$cleanId) ? (int)\$cleanId : 0)
            ->first();
PHP;

$content = str_replace($search, $replace, $content);
file_put_contents($file, $content);
echo "Fixed pos verifyBooking\n";

