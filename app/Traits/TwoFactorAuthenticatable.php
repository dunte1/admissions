<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

trait TwoFactorAuthenticatable
{
    public function hasTwoFactorEnabled(): bool
    {
        return !empty($this->two_factor_secret) && !empty($this->two_factor_confirmed_at);
    }

    public function enableTwoFactor(): array
    {
        $secret = $this->generateTwoFactorSecret();
        $this->two_factor_secret = Crypt::encryptString($secret);
        $this->two_factor_recovery_codes = Crypt::encryptString(json_encode($this->generateRecoveryCodes()));
        $this->save();

        return [
            'secret' => $secret,
            'recovery_codes' => json_decode(Crypt::decryptString($this->two_factor_recovery_codes)),
        ];
    }

    public function disableTwoFactor(): void
    {
        $this->two_factor_secret = null;
        $this->two_factor_recovery_codes = null;
        $this->two_factor_confirmed_at = null;
        $this->save();
    }

    public function confirmTwoFactor(): void
    {
        $this->two_factor_confirmed_at = now();
        $this->save();
    }

    public function verifyTwoFactorCode(string $code): bool
    {
        if (!$this->hasTwoFactorEnabled()) {
            return false;
        }

        $secret = Crypt::decryptString($this->two_factor_secret);
        return $this->TOTPverify($code, $secret);
    }

    public function verifyRecoveryCode(string $code): bool
    {
        if (!$this->hasTwoFactorEnabled()) {
            return false;
        }

        $recoveryCodes = json_decode(Crypt::decryptString($this->two_factor_recovery_codes), true);
        $index = array_search($code, $recoveryCodes);

        if ($index === false) {
            return false;
        }

        unset($recoveryCodes[$index]);
        $this->two_factor_recovery_codes = Crypt::encryptString(json_encode(array_values($recoveryCodes)));
        $this->save();

        return true;
    }

    protected function generateTwoFactorSecret(): string
    {
        return Str::random(32);
    }

    protected function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        return $codes;
    }

    protected function TOTPverify(string $code, string $secret): bool
    {
        $timeSlice = floor(time() / 30);
        
        for ($i = -1; $i <= 1; $i++) {
            $expectedCode = $this->generateTOTPCode($secret, $timeSlice + $i);
            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }
        
        return false;
    }

    protected function generateTOTPCode(string $secret, int $timeSlice): string
    {
        $secretKey = $this->base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $secretKey, true);
        $offset = ord($hash[19]) & 0xf;
        $binary = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        );
        $otp = $binary % 1000000;
        return str_pad((string) $otp, 6, '0', STR_PAD_LEFT);
    }

    protected function base32Decode(string $encoded): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $decoded = '';
        $buffer = 0;
        $bitsLeft = 0;

        for ($i = 0; $i < strlen($encoded); $i++) {
            $char = strtoupper($encoded[$i]);
            if ($char === '=') continue;
            
            $value = strpos($alphabet, $char);
            if ($value === false) continue;

            $buffer = ($buffer << 5) | $value;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $decoded .= chr(($buffer >> $bitsLeft) & 0xff);
            }
        }

        return $decoded;
    }

    public function getTwoFactorQrCodeUrl(): ?string
    {
        if (!$this->two_factor_secret) {
            return null;
        }

        $secret = Crypt::decryptString($this->two_factor_secret);
        $appName = system_setting('app_name', config('app.name'));
        
        return "otpauth://totp/{$appName}:{$this->email}?secret={$secret}&issuer={$appName}";
    }

    public function regenerateRecoveryCodes(): array
    {
        $codes = $this->generateRecoveryCodes();
        $this->two_factor_recovery_codes = Crypt::encryptString(json_encode($codes));
        $this->save();

        return $codes;
    }
}
