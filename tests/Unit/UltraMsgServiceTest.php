<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\WhatsApp\UltraMsgService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UltraMsgServiceTest extends TestCase
{
    public function test_send_otp_normalizes_local_phone_to_international_format(): void
    {
        Http::fake([
            'https://api.ultramsg.com/*' => Http::response(['sent' => true], 200),
        ]);

        config([
            'services.ultramsg.enabled' => true,
            'services.ultramsg.base_url' => 'https://api.ultramsg.com',
            'services.ultramsg.instance_id' => 'instance169733',
            'services.ultramsg.token' => 'token123',
            'services.ultramsg.default_country_code' => '+963',
        ]);

        $user = new User([
            'name' => 'Local Phone',
            'phone' => '0994142438',
        ]);

        app(UltraMsgService::class)->sendOtp($user, '123456');

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.ultramsg.com/instance169733/messages/chat'
                && $request['to'] === '+963994142438'
                && $request['token'] === 'token123';
        });
    }

    public function test_send_otp_accepts_country_code_without_plus(): void
    {
        Http::fake([
            'https://api.ultramsg.com/*' => Http::response(['sent' => true], 200),
        ]);

        config([
            'services.ultramsg.enabled' => true,
            'services.ultramsg.base_url' => 'https://api.ultramsg.com',
            'services.ultramsg.instance_id' => 'instance169733',
            'services.ultramsg.token' => 'token123',
            'services.ultramsg.default_country_code' => '+963',
        ]);

        $user = new User([
            'name' => 'No Plus',
            'phone' => '963994142438',
        ]);

        app(UltraMsgService::class)->sendOtp($user, '123456');

        Http::assertSent(function ($request) {
            return $request['to'] === '+963994142438';
        });
    }
}

