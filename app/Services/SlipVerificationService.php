<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * บริการตรวจสอบสลิปโอนเงินอัตโนมัติผ่าน EasySlip Developer API v2
 * เอกสารอ้างอิง: https://document.easyslip.com/en/v2/
 */
class SlipVerificationService
{
    /**
     * ตรวจสอบสลิปโอนเงิน
     *
     * @param  UploadedFile|string  $slip           ไฟล์รูปภาพสลิป หรือพาธไฟล์
     * @param  float                $expectedAmount ยอดเงินที่คาดหวัง (รวมค่าธรรมเนียมแล้ว)
     * @return array{verified: bool, status_type: string, reason: string, raw: array|null}
     *         status_type: 'verified' | 'invalid_slip' | 'system_error'
     */
    public function verify(UploadedFile|string $slip, float $expectedAmount): array
    {
        $apiKey = config('ticketing.slip_verify_api_key', '');

        if (empty($apiKey)) {
            return $this->result(false, 'system_error', 'ยังไม่ได้ระบุ API Key ของ EasySlip');
        }

        try {
            if ($slip instanceof UploadedFile) {
                $fileContents = file_get_contents($slip->getRealPath());
                $fileName = $slip->getClientOriginalName();
            } else {
                $fileContents = file_get_contents($slip);
                $fileName = basename($slip);
            }

            // ──────────────────────────────────────────────────────────
            // ยิงคำขอไปยัง EasySlip API v2: POST /v2/verify/bank
            // ส่ง image พร้อมพารามิเตอร์ matchAmount ตามสเปก V2
            // ──────────────────────────────────────────────────────────
            $response = Http::timeout(15)
                ->withToken($apiKey)
                ->attach('image', $fileContents, $fileName)
                ->post('https://api.easyslip.com/v2/verify/bank', [
                    'matchAmount' => round($expectedAmount, 2),
                ]);

            $status = $response->status();
            $body = $response->json();

            // ตรวจสอบกรณี Error ตอบกลับตามมาตรฐาน EasySlip V2
            // รูปแบบ: { "success": false, "error": { "code": "ERROR_CODE", "message": "..." } }
            if (! $response->successful() || (isset($body['success']) && $body['success'] === false)) {
                $errorCode = $body['error']['code'] ?? $body['message'] ?? 'UNKNOWN_ERROR';
                $errorMessage = $body['error']['message'] ?? $body['message'] ?? ('HTTP ' . $status);

                Log::warning('EasySlip v2 API Error', [
                    'status' => $status,
                    'code'   => $errorCode,
                    'msg'    => $errorMessage,
                ]);

                // แยกแยะตาม Error Codes Reference ของ EasySlip V2:
                return match ($errorCode) {
                    'SLIP_NOT_FOUND', 'qrcode_not_found' => $this->result(
                        false,
                        'invalid_slip',
                        'ไม่พบ QR Code ในรูปภาพสลิป หรือสลิปไม่ถูกต้องในระบบธนาคาร กรุณานำใบจองไปชำระที่เคาน์เตอร์',
                        $body
                    ),

                    'VALIDATION_ERROR' => $this->result(
                        false,
                        'invalid_slip',
                        'รูปแบบไฟล์หรือข้อมูลสลิปไม่ถูกต้อง กรุณาติดต่อชำระเงินที่เคาน์เตอร์',
                        $body
                    ),

                    'QUOTA_EXCEEDED' => $this->result(
                        false,
                        'system_error',
                        'โควตา API ตรวจสอบสลิปเต็มชั่วคราว เจ้าหน้าที่จะตรวจสอบสลิปให้ท่านโดยเร็ว',
                        $body
                    ),

                    'MISSING_API_KEY', 'INVALID_API_KEY', 'BRANCH_INACTIVE', 'SERVICE_BANNED', 'IP_NOT_ALLOWED' => $this->result(
                        false,
                        'system_error',
                        'เกิดข้อผิดพลาดในการตรวจสอบสิทธิ์ API (เจ้าหน้าที่จะตรวจสอบสลิปให้ท่านด้วยตนเอง)',
                        $body
                    ),

                    'API_SERVER_ERROR' => $this->result(
                        false,
                        'system_error',
                        'เซิร์ฟเวอร์ระบบตรวจสลิปขัดข้องชั่วคราว (เจ้าหน้าที่จะตรวจสอบสลิปให้ภายหลัง)',
                        $body
                    ),

                    default => $this->result(
                        false,
                        'invalid_slip',
                        'การตรวจสอบสลิปไม่ผ่าน: ' . $errorMessage . ' กรุณาชำระที่เคาน์เตอร์',
                        $body
                    ),
                };
            }

            // ──────────────────────────────────────────────────────────
            // กรณีผลลัพธ์สำเร็จ: { "success": true, "data": { ... } }
            // ──────────────────────────────────────────────────────────
            $data = $body['data'] ?? [];

            // ตรวจสอบผลการเปรียบเทียบยอดเงินจาก V2 Amount Validation
            $isAmountMatched = $data['isAmountMatched'] ?? null;
            $amountInSlip = (float) ($data['amountInSlip'] ?? $data['rawSlip']['amount']['amount'] ?? 0);

            if ($isAmountMatched === false || ($isAmountMatched === null && abs($amountInSlip - $expectedAmount) > 1.00)) {
                return $this->result(
                    false,
                    'invalid_slip',
                    sprintf('ยอดเงินในสลิป (%.2f บาท) ไม่ตรงกับยอดที่ต้องชำระ (%.2f บาท) กรุณาชำระเงินที่เคาน์เตอร์', $amountInSlip, $expectedAmount),
                    $body
                );
            }

            // ผ่านการตรวจสอบเรียบร้อยทั้งหมด
            return [
                'verified' => true,
                'status_type' => 'verified',
                'reason' => 'ตรวจสอบสลิปผ่านอัตโนมัติเรียบร้อย',
                'raw' => $body,
            ];

        } catch (\Throwable $e) {
            Log::error('EasySlip V2 Verification Exception', [
                'message' => $e->getMessage(),
            ]);

            return $this->result(
                false,
                'system_error',
                'ไม่สามารถเชื่อมต่อระบบตรวจสอบสลิปได้ (เจ้าหน้าที่จะตรวจสอบให้ภายหลัง)',
                null
            );
        }
    }

    private function result(bool $verified, string $statusType, string $reason, ?array $raw = null): array
    {
        return [
            'verified' => $verified,
            'status_type' => $statusType,
            'reason' => $reason,
            'raw' => $raw,
        ];
    }
}
