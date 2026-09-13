<?php
$file = 'app/Http/Controllers/BookingController.php';
$content = file_get_contents($file);

// Add custom messages array to validate() in create()
$createValidate = <<<EOT
        \$validated = \$request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'visitor_type' => 'required|in:individual,school,company,government',
            'quantity' => 'required|integer|min:1|max:160',
        ], [
            'booker_phone.regex' => 'กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (ตัวเลข 10 หลัก)',
        ]);
EOT;

$content = preg_replace("/\\\$validated = \\\$request->validate\(\[(.*?)\]\);/s", $createValidate, $content, 1);

// Add custom messages array to validate() in store()
$storeValidate = <<<EOT
        \$validated = \$request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'visitor_type' => 'required|in:individual,school,company,government',
            'quantity' => 'required|integer|min:1|max:160',
            'seats' => 'required|array|min:1',
            'seats.*' => 'string|max:10',
            'school_name' => 'nullable|string|max:255',
            'school_type' => 'nullable|string|max:255',
            'education_level' => 'nullable|string|max:255',
            'teachers_count' => 'nullable|integer|min:0',
            'students_count' => 'nullable|integer|min:0',
            'parents_count' => 'nullable|integer|min:0',
            'gov_agency_name' => 'nullable|string|max:255',
            'gov_department' => 'nullable|string|max:255',
            'gov_officers_count' => 'nullable|integer|min:0',
        ], [
            'booker_phone.regex' => 'กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (ตัวเลข 10 หลัก)',
        ]);
EOT;

// I have to replace the second match carefully.
// Actually, it's easier to just use my replace_file_content if I know the exact lines, but wait, the file has corrupted characters in PowerShell output.

