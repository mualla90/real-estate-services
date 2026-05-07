# Pusher + FCM + API Resources - Complete Guide

## 1) Pusher (Realtime Chat)

## 1.1 Why we use Pusher
Pusher is used for realtime events so chat messages appear instantly without waiting for manual refresh.

In this project, Pusher is used for:
1. Realtime chat message broadcasting.

## 1.2 Where it is configured
1. `.env`:
```env
BROADCAST_CONNECTION=pusher
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
PUSHER_SCHEME=https
PUSHER_PORT=443
```

2. Broadcasting config:
- `config/broadcasting.php`

3. Broadcast channels:
- `routes/channels.php`

## 1.3 Runtime flow (chat)
1. User sends message via API:
   - `POST /api/business-accounts/{businessAccount}/conversations/{conversation}/messages`
2. Controller calls chat service to persist message.
3. Controller triggers event:
   - `event(new MessageSent($message));`
4. Event is broadcast through Pusher.
5. Mobile/web clients subscribed to the channel receive message instantly.

Code:
- Event: `app/Events/MessageSent.php`
- Trigger point: `app/Http/Controllers/Api/ConversationController.php`

## 1.4 How to test Pusher quickly
1. Set valid Pusher credentials in `.env`.
2. Clear config cache:
   - `php artisan config:clear`
3. Send message using chat endpoint.
4. Confirm:
   - message saved in DB
   - event appears in Pusher debug console
   - connected client receives realtime payload.

## 1.5 Common Pusher issues
1. No events:
   - wrong `BROADCAST_CONNECTION`
   - invalid app key/secret
2. Auth/channel errors:
   - channel permissions not matching current user/business account
3. Works in DB but not realtime:
   - frontend not subscribed to same channel/event name.

---

## 2) Firebase FCM (Push Notifications)

## 2.1 Why we use FCM
FCM is used to push notifications to device tokens (mobile/web push), especially when app is background or closed.

This is different from dashboard notification history:
1. Dashboard history = DB notifications
2. Device push = FCM

## 2.2 Where it is configured
1. `.env` (v1):
```env
FCM_PROJECT_ID=your_project_id
FCM_SERVICE_ACCOUNT_JSON=storage/app/firebase-service-account.json
FCM_V1_ENDPOINT=https://fcm.googleapis.com/v1/projects
```

2. Optional legacy fallback:
```env
FCM_SERVER_KEY=your_legacy_server_key
FCM_ENDPOINT=https://fcm.googleapis.com/fcm/send
```

3. `config/services.php` -> `fcm` section.

## 2.3 Service account file
1. Generate Firebase service account JSON from Firebase Console.
2. Put file at:
   - `storage/app/firebase-service-account.json`
3. Ensure file is git-ignored (do not commit secrets).

## 2.4 Runtime flow (notification)
1. App creates database notification record (`app_notifications`).
2. `NotificationService` checks if notifiable has `fcm_token`.
3. It tries **FCM v1** first:
   - load service account
   - create JWT
   - exchange for OAuth access token
   - send `messages:send` request
4. If v1 fails and legacy key exists, fallback to legacy endpoint.

Code:
- `app/Services/Notification/NotificationService.php`

## 2.5 Where notifications are triggered
Examples:
1. Business account review status changes.
2. Service review status changes.
3. New service request to provider.

The service method used:
1. `notifyUser(...)`
2. `notifyAdminsByPermission(...)`

## 2.6 FCM token requirement
1. If you want mobile push: device must send/store `fcm_token`.
2. If dashboard only: FCM token is not required; DB notifications are enough.

## 2.7 How to test FCM
1. Ensure user has `fcm_token` in DB.
2. Trigger a notification event (approve/reject/new request).
3. Confirm:
   - record exists in `app_notifications`
   - push call succeeds in logs
   - device receives notification.

## 2.8 Common FCM issues
1. Invalid service account path.
2. Wrong project id.
3. Expired/invalid device token.
4. Missing permissions in service account.
5. Token fetch blocked by network/firewall.

---

## 3) Pusher vs FCM (When to use which)
1. Use Pusher for:
   - instant in-app realtime updates (chat, live counters).
2. Use FCM for:
   - push to mobile device outside active session.
3. Use DB notifications for:
   - history, read/unread, ownership checks.

---

## 4) API Resources in This Project

## 4.1 Why we use API Resources
API Resources standardize response payload shape and keep controllers clean.

## 4.2 Resource files
Under:
- `app/Http/Resources/`

Main resources used:
1. `ServiceResource`
2. `SliderResource`
3. `ReviewResource`
4. `FavoriteResource`
5. `ReportResource`
6. `ServiceRequestResource`
7. `ConversationResource`
8. `MessageResource`
9. `AppNotificationResource`

## 4.3 Where each resource is used
1. Services browse/detail/business services:
   - Controller: `ServiceBrowseController`, `BusinessAccountServiceController`
   - Resource: `ServiceResource`

2. Sliders:
   - Controller: `SliderController`
   - Resource: `SliderResource`

3. Reviews:
   - Controller: `ReviewController`
   - Resource: `ReviewResource`

4. Favorites:
   - Controller: `FavoriteController`
   - Resource: `FavoriteResource`

5. Reports:
   - Controller: `ReportController`
   - Resource: `ReportResource`

6. Service requests:
   - Controller: `ServiceRequestController`
   - Resource: `ServiceRequestResource`

7. Chat:
   - Controller: `ConversationController`
   - Resources: `ConversationResource`, `MessageResource`

8. Notifications:
   - Controller: `NotificationController`
   - Resource: `AppNotificationResource`

## 4.4 Resource behavior examples
1. `ServiceResource` includes:
   - localized fields
   - media URLs
   - related category/subcategory/city
   - dynamic field values
   - rating counters

2. `ReviewResource` includes:
   - rating/comment
   - reviewer business account info

3. `AppNotificationResource` includes:
   - type/title/message/data
   - read status timestamps

---

## 5) End-to-End Scenario (Pusher + FCM + Resource)
1. User A sends chat message:
   - saved in DB
   - returned via `MessageResource`
   - broadcast via `MessageSent` (Pusher)
2. User B receives new service request:
   - notification row created
   - if B has `fcm_token`, push sent via FCM
   - list endpoint returns via `AppNotificationResource`

---

## 6) Security Notes
1. Keep Pusher/FCM/UtraMsg secrets in `.env` only.
2. Do not commit service account JSON.
3. Rotate credentials if accidentally shared.
4. Restrict notification ownership (already implemented in controllers).

---

## 7) Quick Troubleshooting Checklist
1. Realtime chat not working:
   - check `BROADCAST_CONNECTION=pusher`
   - verify event is fired and channel subscribed
2. Push not arriving:
   - verify `fcm_token` exists for user
   - check FCM service account/project id
3. API shape mismatch:
   - check corresponding Resource class used by controller response

