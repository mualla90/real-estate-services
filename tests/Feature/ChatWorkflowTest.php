<?php

namespace Tests\Feature;

use App\Models\ActivityType;
use App\Models\BusinessAccount;
use App\Models\Category;
use App\Models\City;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Service;
use App\Models\Subcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ChatWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_conversation_for_visible_service(): void
    {
        [$requesterUser, $requesterBusiness, $providerBusiness, $service] = $this->createChatContext();

        Passport::actingAs($requesterUser);
        $response = $this->postJson("/api/business-accounts/{$requesterBusiness->id}/conversations", [
            'service_id' => $service->id,
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('message', __('api.chat.conversation_created'));

        $this->assertDatabaseHas('conversations', [
            'service_id' => $service->id,
            'initiator_business_account_id' => $requesterBusiness->id,
            'recipient_business_account_id' => $providerBusiness->id,
        ]);
    }

    public function test_can_send_message_in_conversation(): void
    {
        [$requesterUser, $requesterBusiness, $providerBusiness, $service] = $this->createChatContext();

        $conversation = Conversation::query()->create([
            'service_id' => $service->id,
            'initiator_business_account_id' => $requesterBusiness->id,
            'recipient_business_account_id' => $providerBusiness->id,
        ]);

        Passport::actingAs($requesterUser);
        $response = $this->postJson("/api/business-accounts/{$requesterBusiness->id}/conversations/{$conversation->id}/messages", [
            'body' => 'Hello from requester',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('message', __('api.chat.message_sent'));

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_business_account_id' => $requesterBusiness->id,
            'body' => 'Hello from requester',
            'status' => 'sent',
        ]);
    }

    public function test_mark_read_updates_only_received_sent_messages(): void
    {
        [$requesterUser, $requesterBusiness, $providerBusiness, $service, $providerUser] = $this->createChatContext(withProviderUser: true);

        $conversation = Conversation::query()->create([
            'service_id' => $service->id,
            'initiator_business_account_id' => $requesterBusiness->id,
            'recipient_business_account_id' => $providerBusiness->id,
        ]);

        Message::query()->create([
            'conversation_id' => $conversation->id,
            'sender_business_account_id' => $requesterBusiness->id,
            'body' => 'Unread 1',
            'status' => 'sent',
        ]);
        Message::query()->create([
            'conversation_id' => $conversation->id,
            'sender_business_account_id' => $requesterBusiness->id,
            'body' => 'Unread 2',
            'status' => 'sent',
        ]);
        Message::query()->create([
            'conversation_id' => $conversation->id,
            'sender_business_account_id' => $providerBusiness->id,
            'body' => 'Own message',
            'status' => 'sent',
        ]);

        Passport::actingAs($providerUser);
        $response = $this->patchJson("/api/business-accounts/{$providerBusiness->id}/conversations/{$conversation->id}/read");

        $response->assertOk();
        $response->assertJsonPath('message', __('api.chat.messages_marked_read'));
        $response->assertJsonPath('data.updated_count', 2);

        $this->assertSame(2, Message::query()->where('conversation_id', $conversation->id)->where('status', 'read')->count());
        $this->assertSame(
            1,
            Message::query()
                ->where('conversation_id', $conversation->id)
                ->where('sender_business_account_id', $providerBusiness->id)
                ->where('status', 'sent')
                ->count()
        );
    }

    public function test_non_owner_cannot_access_other_business_account_conversations(): void
    {
        [$requesterUser, $requesterBusiness, $providerBusiness] = $this->createChatContext();

        Passport::actingAs($requesterUser);
        $response = $this->getJson("/api/business-accounts/{$providerBusiness->id}/conversations");

        $response->assertStatus(403);
    }

    protected function createChatContext(bool $withProviderUser = false): array
    {
        $city = City::query()->create([
            'name' => ['en' => 'Damascus', 'ar' => 'دمشق'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $activityType = ActivityType::query()->create([
            'name' => ['en' => 'Broker', 'ar' => 'وسيط'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $category = Category::query()->create([
            'name' => ['en' => 'Real Estate', 'ar' => 'عقارات'],
            'description' => ['en' => 'Main', 'ar' => 'رئيسي'],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $subcategory = Subcategory::query()->create([
            'category_id' => $category->id,
            'name' => ['en' => 'Apartments', 'ar' => 'شقق'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $requesterUser = User::factory()->create();
        $requesterBusiness = BusinessAccount::query()->create([
            'user_id' => $requesterUser->id,
            'activity_type_id' => $activityType->id,
            'city_id' => $city->id,
            'license_number' => 'LIC-CHAT-1',
            'name' => ['en' => 'Requester BA', 'ar' => 'حساب الطالب'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'phone' => '0999991111',
            'email' => 'requester-chat@example.com',
            'address' => 'Address',
            'status' => 'approved',
        ]);

        $providerUser = User::factory()->create();
        $providerBusiness = BusinessAccount::query()->create([
            'user_id' => $providerUser->id,
            'activity_type_id' => $activityType->id,
            'city_id' => $city->id,
            'license_number' => 'LIC-CHAT-2',
            'name' => ['en' => 'Provider BA', 'ar' => 'حساب المزود'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'phone' => '0999992222',
            'email' => 'provider-chat@example.com',
            'address' => 'Address',
            'status' => 'approved',
        ]);

        $service = Service::query()->create([
            'business_account_id' => $providerBusiness->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory->id,
            'city_id' => $city->id,
            'title' => ['en' => 'Provider Service', 'ar' => 'خدمة المزود'],
            'description' => ['en' => 'Desc', 'ar' => 'وصف'],
            'service_type' => 'sale',
            'price' => 350,
            'currency' => 'USD',
            'price_usd' => 350,
            'price_syp' => 4550000,
            'status' => 'approved',
            'published_at' => now(),
            'is_active' => true,
            'sort_order' => 0,
        ]);

        if ($withProviderUser) {
            return [$requesterUser, $requesterBusiness, $providerBusiness, $service, $providerUser];
        }

        return [$requesterUser, $requesterBusiness, $providerBusiness, $service];
    }
}
