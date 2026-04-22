<?php

namespace App\Services\WhatsApp;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UltraMsgService
{
    public function sendOtp(User $user, string $code): void
    {
        if (! config('services.ultramsg.enabled')) {
            return;
        }

        $token = (string) config('services.ultramsg.token');
        $to = $this->normalizePhone($user->phone);
        $body = $this->buildMessage($user->name, $code);
        $url = $this->buildMessagesUrl();

        if ($token === '' || $to === '' || $url === '') {
            Log::warning('UltraMsg OTP skipped due to missing configuration.', [
                'user_id' => $user->id,
            ]);

            return;
        }

        $timeout = (int) config('services.ultramsg.timeout', 10);

        Http::timeout($timeout)
            ->asForm()
            ->post($url, [
                'token' => $token,
                'to' => $to,
                'body' => $body,
            ])
            ->throw();
    }

    protected function buildMessage(string $name, string $code): string
    {
        $template = (string) config('services.ultramsg.otp_template', 'Hello :name, your OTP code is :code');

        return str_replace(
            [':name', ':code'],
            [$name, $code],
            $template
        );
    }

    protected function buildMessagesUrl(): string
    {
        $baseUrl = rtrim((string) config('services.ultramsg.base_url', ''), '/');
        $instanceId = trim((string) config('services.ultramsg.instance_id', ''));

        if ($baseUrl === '') {
            return '';
        }

        if ($instanceId !== '' && ! str_ends_with($baseUrl, '/'.$instanceId)) {
            $baseUrl .= '/'.$instanceId;
        }

        return $baseUrl.'/messages/chat';
    }

    protected function normalizePhone(?string $phone): string
    {
        if (! $phone) {
            return '';
        }

        $normalized = preg_replace('/[^\d+]/', '', trim($phone)) ?? '';

        if ($normalized === '') {
            return '';
        }

        if (str_starts_with($normalized, '+')) {
            return $normalized;
        }

        if (str_starts_with($normalized, '00')) {
            return '+'.substr($normalized, 2);
        }

        $countryCode = (string) config('services.ultramsg.default_country_code', '+963');
        $countryCode = str_starts_with($countryCode, '+') ? $countryCode : '+'.$countryCode;
        $countryDigits = ltrim($countryCode, '+');

        if (str_starts_with($normalized, $countryDigits)) {
            return '+'.$normalized;
        }

        if (str_starts_with($normalized, '0')) {
            return $countryCode.substr($normalized, 1);
        }

        return $countryCode.$normalized;
    }
}
