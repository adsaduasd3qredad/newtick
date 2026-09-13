@extends('pos.layout')
@section('title', 'เช็คคิวอาร์ (Scan QR)')
@section('content')
<div class="max-w-2xl mx-auto py-10 px-4">
    <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <h2 class="text-xl font-bold mb-4 text-center">สแกนรหัสการจองออนไลน์</h2>
        
        @if($errors->any())
            <div class="bg-rose-50 text-rose-600 p-4 rounded-xl mb-4 text-sm font-semibold text-center border border-rose-200">
                {{$errors->first()}}
            </div>
        @endif

        <form action="{{ route('pos.verify') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">รหัสการจอง (QR Ref)</label>
                <input type="text" name="ref" id="refInput" autofocus required autocomplete="off"
                    class="w-full text-center py-4 px-4 border-2 border-cyan-300 focus:border-cyan-500 focus:ring-cyan-500 rounded-xl text-lg font-bold"
                    placeholder="สแกนหรือพิมพ์รหัสที่นี่...">
            </div>
            <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-3 rounded-xl transition">
                ตรวจสอบข้อมูล
            </button>
        </form>
    </div>
</div>
@endsection