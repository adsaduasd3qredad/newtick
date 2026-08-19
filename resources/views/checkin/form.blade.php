<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เช็คอินรับตั๋ว - เคาน์เตอร์</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-900 text-white p-8 max-w-lg mx-auto">
    <h1 class="text-xl font-bold mb-6">เช็คอินรับตั๋ว (เจ้าหน้าที่)</h1>

    @if ($errors->any())
        <div class="bg-red-900 text-red-200 p-3 rounded mb-4">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('checkin.process') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm mb-1">สแกน QR หรือกรอกรหัสการจอง</label>
            <input type="text" name="qr_ticket_ref" id="qr_input" autofocus required
                class="w-full bg-gray-800 rounded p-3 text-lg">
        </div>
        <button type="submit" class="w-full bg-green-600 py-3 rounded font-medium text-lg">
            ยืนยันรับตั๋ว
        </button>
    </form>

    <script>
        // ให้ auto-focus กลับที่ช่องกรอกเสมอ เผื่อใช้เครื่องสแกนบาร์โค้ดต่อ USB
        document.getElementById('qr_input').focus();
    </script>
</body>
</html>