<?php

namespace App\Services;

class PromptPayQr
{
    public static function generatePayload(string $target, float $amount): string
    {
        $target = self::formatTarget($target);

        $payload  = self::tlv('00', '01');
        $payload .= self::tlv('01', '12'); // 12 = dynamic QR (มีจำนวนเงินฝังอยู่)

        $merchantInfo  = self::tlv('00', 'A000000677010111'); // PromptPay AID (ค่าคงที่)
        $merchantInfo .= self::tlv(strlen($target) === 13 ? '01' : '02', $target);
        $payload .= self::tlv('29', $merchantInfo);

        $payload .= self::tlv('53', '764'); // รหัสสกุลเงินบาท
        $payload .= self::tlv('54', number_format($amount, 2, '.', ''));
        $payload .= self::tlv('58', 'TH');

        $payload .= '6304';
        $payload .= self::crc16($payload);

        return $payload;
    }

    private static function formatTarget(string $target): string
    {
        $target = preg_replace('/[^0-9]/', '', $target);

        if (strlen($target) === 10) {
            // เบอร์โทร 10 หลัก เช่น 0812345678 -> แปลงเป็น 0066812345678
            $target = '0066' . substr($target, 1);
        }

        return $target;
    }

    private static function tlv(string $id, string $value): string
    {
        $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
        return $id . $length . $value;
    }

    private static function crc16(string $data): string
    {
        $crc = 0xFFFF;
        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                $crc = ($crc & 0x8000) ? (($crc << 1) ^ 0x1021) : ($crc << 1);
                $crc &= 0xFFFF;
            }
        }
        return strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
    }
}