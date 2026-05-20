<?php

namespace App\Services\Notification;

use App\Models\Admin;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function notifyUser(
        User $user,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): AppNotification {
        return $this->notifyNotifiable($user, $type, $title, $message, $data);
    }

    public function notifyAdminsByPermission(
        string $permission,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): void {
        $admins = Admin::query()
            ->where('is_active', true)
            ->get()
            ->filter(fn (Admin $admin) => $admin->can($permission));

        foreach ($admins as $admin) {
            $this->notifyNotifiable($admin, $type, $title, $message, $data);
        }
    }

    protected function notifyNotifiable(
        mixed $notifiable,
        string $type,
        string $title,
        string $message,
        array $data = []
    ): AppNotification {
        $notification = $notifiable->appNotifications()->create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);

        $this->sendPushIfPossible($notifiable, $title, $message, $data);

        return $notification;
    }

    protected function sendPushIfPossible(mixed $notifiable, string $title, string $message, array $data = []): void
    {
        $deviceToken = data_get($notifiable, 'fcm_token');
        if (empty($deviceToken)) {
            return;
        }

        if ($this->sendPushUsingV1($deviceToken, $title, $message, $data, $notifiable)) {
            return;
        }

        $this->sendPushUsingLegacy($deviceToken, $title, $message, $data, $notifiable);
    }

    protected function sendPushUsingV1(
        string $deviceToken,
        string $title,
        string $message,
        array $data,
        mixed $notifiable
    ): bool {
        $credentials = $this->resolveServiceAccountCredentials();
        if (! $credentials) {
            return false;
        }

        $projectId = config('services.fcm.project_id') ?: ($credentials['project_id'] ?? null);
        if (! $projectId) {
            Log::warning('FCM v1 skipped: project_id is missing.');

            return false;
        }

        $accessToken = $this->getV1AccessToken($credentials);
        if (! $accessToken) {
            return false;
        }

        $endpoint = rtrim((string) config('services.fcm.v1_endpoint', 'https://fcm.googleapis.com/v1/projects'), '/')
            . '/'
            . $projectId
            . '/messages:send';

        try {
            Http::withToken($accessToken)
                ->acceptJson()
                ->post($endpoint, [
                    'message' => [
                        'token' => $deviceToken,
                        'notification' => [
                            'title' => $title,
                            'body' => $message,
                        ],
                        'data' => $this->normalizeDataForFcm($data),
                    ],
                ])
                ->throw();

            return true;
        } catch (\Throwable $e) {
            Log::warning('FCM v1 notification dispatch failed.', [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => data_get($notifiable, 'id'),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function sendPushUsingLegacy(
        string $deviceToken,
        string $title,
        string $message,
        array $data,
        mixed $notifiable
    ): void {
        $serverKey = config('services.fcm.server_key');
        if (empty($serverKey)) {
            return;
        }

        $endpoint = config('services.fcm.endpoint', 'https://fcm.googleapis.com/fcm/send');

        try {
            Http::withToken($serverKey)
                ->acceptJson()
                ->post($endpoint, [
                    'to' => $deviceToken,
                    'notification' => [
                        'title' => $title,
                        'body' => $message,
                    ],
                    'data' => $this->normalizeDataForFcm($data),
                ])
                ->throw();
        } catch (\Throwable $e) {
            Log::warning('FCM notification dispatch failed.', [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => data_get($notifiable, 'id'),
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function resolveServiceAccountCredentials(): ?array
    {
        $raw = (string) config('services.fcm.service_account_json');
        if ($raw === '') {
            return null;
        }

        $json = str_starts_with(trim($raw), '{')
            ? $raw
            : (is_file($raw) ? file_get_contents($raw) : false);

        if (! $json) {
            Log::warning('FCM v1 skipped: service account JSON path is invalid.', [
                'path' => $raw,
            ]);

            return null;
        }

        $credentials = json_decode($json, true);
        if (! is_array($credentials)) {
            Log::warning('FCM v1 skipped: service account JSON is invalid.');

            return null;
        }

        if (empty($credentials['client_email']) || empty($credentials['private_key'])) {
            Log::warning('FCM v1 skipped: missing client_email/private_key in service account JSON.');

            return null;
        }

        return $credentials;
    }

    protected function getV1AccessToken(array $credentials): ?string
    {
        $cacheKey = 'fcm.v1.access_token.'.md5((string) ($credentials['client_email'] ?? 'default'));
        $cachedToken = Cache::get($cacheKey);
        if (is_string($cachedToken) && $cachedToken !== '') {
            return $cachedToken;
        }

        $tokenUri = (string) ($credentials['token_uri'] ?? 'https://oauth2.googleapis.com/token');
        $issuedAt = time();
        $expiresAt = $issuedAt + 3600;

        $jwtHeader = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ]));

        $jwtPayload = $this->base64UrlEncode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $tokenUri,
            'iat' => $issuedAt,
            'exp' => $expiresAt,
        ]));

        $signingInput = $jwtHeader.'.'.$jwtPayload;
        $signature = '';
        $privateKey = (string) $credentials['private_key'];

        if (! openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            Log::warning('FCM v1 skipped: failed to sign JWT assertion.');

            return null;
        }

        $assertion = $signingInput.'.'.$this->base64UrlEncode($signature);

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->post($tokenUri, [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $assertion,
                ])
                ->throw()
                ->json();

            $accessToken = data_get($response, 'access_token');
            $expiresIn = (int) data_get($response, 'expires_in', 3600);
            if (! is_string($accessToken) || $accessToken === '') {
                return null;
            }

            Cache::put($cacheKey, $accessToken, now()->addSeconds(max(60, $expiresIn - 120)));

            return $accessToken;
        } catch (\Throwable $e) {
            Log::warning('FCM v1 token fetch failed.', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    protected function normalizeDataForFcm(array $data): array
    {
        $normalized = [];

        foreach ($data as $key => $value) {
            if (is_array($value) || is_object($value)) {
                $normalized[(string) $key] = json_encode($value, JSON_UNESCAPED_UNICODE);
                continue;
            }

            if (is_bool($value)) {
                $normalized[(string) $key] = $value ? '1' : '0';
                continue;
            }

            if ($value === null) {
                $normalized[(string) $key] = '';
                continue;
            }

            $normalized[(string) $key] = (string) $value;
        }

        return $normalized;
    }

    protected function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
