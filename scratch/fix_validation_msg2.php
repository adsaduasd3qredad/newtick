<?php
$file = 'app/Http/Controllers/BookingController.php';
$content = file_get_contents($file);
$content = preg_replace("/^\xEF\xBB\xBF/", '', $content);

$search1 = "        \$validated = \$request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'visitor_type' => 'required|in:individual,school,company,government',
            'quantity' => 'required|integer|min:1|max:160',
        ]);";
$replace1 = "        \$validated = \$request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'booker_name' => 'required|string|max:255',
            'booker_email' => 'required|email',
            'booker_phone' => 'required|string|regex:/^[0-9]{10}$/',
            'visitor_type' => 'required|in:individual,school,company,government',
            'quantity' => 'required|integer|min:1|max:160',
        ], [
            'booker_phone.regex' => 'กรุณากรอกเบอร์โทรศัพท์ให้ถูกต้อง (ตัวเลข 10 หลัก)',
        ]);";

$search2 = "        \$validated = \$request->validate([
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
        ]);";
$replace2 = "        \$validated = \$request->validate([
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
        ]);";

$content = str_replace($search1, $replace1, $content);
$content = str_replace($search2, $replace2, $content);

file_put_contents($file, $content);
echo "Added custom messages\n";

