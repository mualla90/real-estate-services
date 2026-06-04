# Realtime Notifications and Chat Demo

This document explains the realtime work added to the project: dashboard Firebase notifications and a Pusher-powered web chat demo for business accounts.

## 1. Dashboard Firebase Notifications

### Goal

Admins should receive browser push notifications when important backend events happen, such as a new business account waiting for review.

### Main Files

- `resources/views/layouts/admin.blade.php`
- `resources/views/partials/firebase.blade.php`
- `public/firebase-messaging-sw.js`
- `app/Http/Controllers/Admin/FcmTokenController.php`
- `app/Models/AdminFcmToken.php`
- `database/migrations/2026_05_24_000000_create_admin_fcm_tokens_table.php`
- `app/Services/Notification/NotificationService.php`
- `routes/web.php`

### Step 1: Include Firebase On Admin Pages

The admin layout includes the Firebase partial:

```blade
@include('partials.firebase')
```

It also includes the CSRF token meta tag, which the JavaScript needs when posting the browser token to Laravel:

```blade
<meta name="csrf-token" content="{{ csrf_token() }}">
```

### Step 2: Register The Browser With Firebase

`resources/views/partials/firebase.blade.php` loads Firebase JavaScript, registers the service worker, asks for notification permission, gets an FCM token, and sends it to Laravel:

```text
POST /fcm/register-token
```

That token represents this browser/device for the logged-in admin.

### Step 3: Store Admin FCM Tokens

We added a table:

```text
admin_fcm_tokens
```

Important columns:

```text
admin_id
token
user_agent
ip_address
last_used_at
```

Why a separate table? One admin can use multiple browsers/devices. A single `fcm_token` column on `admins` would overwrite old tokens.

### Step 4: Save Tokens In Laravel

`FcmTokenController` receives the token and stores it:

```php
AdminFcmToken::query()->updateOrCreate(
    ['token' => $validated['token']],
    [
        'admin_id' => auth('admin')->id(),
        'user_agent' => $request->userAgent(),
        'ip_address' => $request->ip(),
        'last_used_at' => now(),
    ]
);
```

The route is:

```php
Route::post('/fcm/register-token', [FcmTokenController::class, 'store'])
    ->middleware('auth:admin')
    ->name('admin.fcm-token.store');
```

### Step 5: Send Notifications

The central notification service is:

```text
app/Services/Notification/NotificationService.php
```

When a notification is created, it always saves a database notification first:

```php
$notification = $notifiable->appNotifications()->create([...]);
```

Then it tries to send push notifications:

```php
$this->sendPushIfPossible($notifiable, $title, $message, $data);
```

For normal users, it uses:

```text
users.fcm_token
```

For admins, it sends to every token in:

```text
admin_fcm_tokens
```

### Step 6: Business Account Review Notification

When a business account is created, the app calls:

```php
$this->notifications->notifyAdminsByPermission(
    'business-accounts.approve',
    'business_account_pending_review',
    ...
);
```

This creates a database notification and sends FCM push to admins who can approve business accounts.

### Step 7: Service Worker Handles Background Notifications

`public/firebase-messaging-sw.js` receives background Firebase messages and shows the browser notification:

```js
self.registration.showNotification(title, {
    body,
    icon,
    data
});
```

It also handles notification clicks and opens/focuses:

```text
/admin/dashboard
```

## 2. Pusher Chat Demo

### Goal

The mobile frontend does not exist yet, so we added an admin web page to visually prove Pusher chat works.

The chat is between business accounts, because the real chat system is designed around business accounts, not direct users.

### Main Files

- `app/Services/Chat/ChatService.php`
- `app/Http/Controllers/Api/ConversationController.php`
- `app/Events/MessageSent.php`
- `app/Http/Controllers/Admin/ChatDemoController.php`
- `resources/views/admin/chat-demo/index.blade.php`
- `resources/js/app.js`
- `routes/web.php`
- `routes/channels.php`
- `package.json`

### Step 1: Existing Chat Backend

The existing chat data model:

```text
conversations
- service_id
- initiator_business_account_id
- recipient_business_account_id
- last_message_at

messages
- conversation_id
- sender_business_account_id
- body
- status
- read_at
```

The existing service is:

```text
app/Services/Chat/ChatService.php
```

It can:

- create conversations
- list conversations
- list messages
- send messages
- mark messages as read

### Step 2: Existing Message Broadcast

The event:

```text
app/Events/MessageSent.php
```

broadcasts a message immediately using:

```php
ShouldBroadcastNow
```

It sends to:

```php
new PrivateChannel('conversation.'.$this->message->conversation_id)
```

The frontend listens to:

```text
private-conversation.{conversationId}
```

Event name:

```php
message.sent
```

### Step 3: Install Frontend Realtime Packages

We installed:

```bash
npm install laravel-echo pusher-js
```

These are used by `resources/js/app.js` to subscribe to Pusher channels.

### Step 4: Add Admin Chat Demo Page

The admin route:

```text
/admin/chat-demo
```

Controller:

```text
app/Http/Controllers/Admin/ChatDemoController.php
```

View:

```text
resources/views/admin/chat-demo/index.blade.php
```

The page lets an admin:

- choose a requester business account
- choose a service owned by another business account
- create/open a conversation
- send messages as either business account
- see messages appear live

### Step 5: Private Channel Auth For The Demo

The demo uses private Pusher channels. The admin page authenticates using:

```text
POST /admin/chat-demo/pusher/auth
```

This route authorizes:

```text
private-conversation.{conversationId}
private-business-account.{businessAccountId}
```

For the admin demo, any logged-in admin can subscribe to these demo channels.

### Step 6: Live Conversation Messages

In `resources/js/app.js`, the page subscribes to:

```js
echo.private(`conversation.${conversationId}`)
    .listen('.message.sent', (message) => {
        appendMessage(message);
    });
```

When a message is sent, the controller does:

```php
event(new MessageSent($message));
```

The browser receives the message and appends it without refreshing.

## 3. Inbox Counters For Messages Outside The Open Conversation

### Problem

If the user is not currently viewing a specific conversation, they still need to know that a new message arrived.

### Solution

We broadcast each message to two channels:

```text
private-conversation.{conversationId}
private-business-account.{recipientBusinessAccountId}
```

The first updates the open chat.

The second updates inbox/unread counters.

### Event Update

`MessageSent.php` now returns both channels:

```php
return [
    new PrivateChannel('conversation.'.$this->message->conversation_id),
    new PrivateChannel('business-account.'.$recipientBusinessAccountId),
];
```

### API Channel Auth

`routes/channels.php` includes:

```php
Broadcast::channel('business-account.{businessAccountId}', function ($user, int $businessAccountId) {
    return $user->businessAccounts()
        ->whereKey($businessAccountId)
        ->exists();
});
```

This is for the real API/mobile side.

### Demo Channel Auth

`ChatDemoController` authorizes admin demo subscriptions for:

```text
private-business-account.{id}
```

### Frontend Counter Update

The demo subscribes to business account channels:

```js
echo.private(`business-account.${businessAccountId}`)
    .listen('.message.sent', (message) => {
        incrementBadge(...);
    });
```

The page shows:

- live inbox counters per business account
- live badges beside recent conversations

## 4. How To Test

### Firebase Notification Test

1. Login to dashboard.
2. Allow browser notifications.
3. Make sure `admin_fcm_tokens` has a token row.
4. Create a business account from Postman.
5. The admin should receive:
   - database notification
   - browser push notification

### Pusher Chat Demo Test

1. Make sure `.env` has:

```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=...
PUSHER_APP_KEY=...
PUSHER_APP_SECRET=...
PUSHER_APP_CLUSTER=...
```

2. Run Vite if using dev mode:

```bash
npm run dev
```

3. Open:

```text
http://127.0.0.1:8000/admin/chat-demo
```

4. Create/open a conversation.
5. Open the same page in another tab.
6. Send a message from one side.
7. The other tab should update live.

To test inbox counters:

1. Open one conversation.
2. Send a message in another conversation or to another business account.
3. Watch the live inbox counters increment.

## 5. Verification Commands Used

```bash
php artisan route:list --path=admin/chat-demo
php artisan test --filter=ChatWorkflowTest
npm run build
```

The Sass deprecation warnings during build come from dependencies and do not mean the build failed.
