# Real Estate Services - Full Implementation Steps Guide

## 1) Project Setup (From Zero)
1. Create Laravel project and configure `.env` database.
2. Install authentication/token package (Passport) and run migrations.
3. Create base modules:
   - Users
   - Admins
   - Roles/Permissions
4. Configure localization:
   - `lang/en/*`
   - `lang/ar/*`
   - locale middleware for web and API.
5. Create base API routing structure and admin routing structure.

---

## 2) Authentication + OTP (WhatsApp UltraMsg)

## 2.1 What we implemented
- Register
- Login
- Verify OTP
- Resend OTP
- Logout
- Me
- Update Profile

## 2.2 OTP flow design
1. User registers with phone + password.
2. System creates OTP code (6 digits), expiry (5 minutes), one-time use.
3. System sends OTP via WhatsApp (UltraMsg).
4. User verifies OTP with `/auth/verify-otp`.
5. User can login after phone verification.

## 2.3 UltraMsg setup from zero
1. Create account at `https://ultramsg.com`.
2. Create instance (example: `instance169733`).
3. Scan QR to connect WhatsApp account.
4. Copy:
   - instance id
   - token
5. Add env variables:

```env
ULTRAMSG_ENABLED=true
ULTRAMSG_BASE_URL=https://api.ultramsg.com
ULTRAMSG_INSTANCE_ID=instance169733
ULTRAMSG_TOKEN=your_token
ULTRAMSG_OTP_TEMPLATE="Hello :name, your OTP code is :code"
ULTRAMSG_DEFAULT_COUNTRY_CODE=+963
ULTRAMSG_TIMEOUT=10
ULTRAMSG_FAIL_SILENTLY=true
```

## 2.4 How we send message in code
1. OTP is generated in `OtpService::sendVerificationOtp`.
2. OTP service calls `UltraMsgService::sendOtp($user, $otpCode)`.
3. `UltraMsgService` sends POST to:
   - `https://api.ultramsg.com/{instance_id}/messages/chat`
4. Payload:
   - `token`
   - `to` (normalized phone)
   - `body` (template with name/code)
5. Phone normalization:
   - `099...` -> `+96399...`
   - `963...` -> `+963...`
   - `+...` kept as is.

## 2.5 Security hardening we applied
- OTP is no longer returned in API responses.
- Auth routes are throttled.
- Fail-silent option controlled by env.

---

## 3) Roles & Permissions
1. Install and configure Spatie Permission.
2. Define permission set for all admin modules.
3. Protect admin routes using middleware:
   - `permission:xxx,admin`
4. Build role CRUD + assign permissions.
5. Build admin CRUD + assign role.

---

## 4) Business Accounts Workflow
1. User creates business account (with activity type, city, license, names, details).
2. Status starts as `pending`.
3. Admin reviews:
   - approve -> `approved`
   - reject -> `rejected`
4. User cannot create service unless business account is approved.

---

## 5) Categories, Subcategories, Dynamic Fields
1. Admin creates categories.
2. Admin creates subcategories linked to parent category.
3. Admin creates dynamic fields linked as:
   - category only
   - subcategory only
   - both
4. Validation rules enforce:
   - subcategory belongs to category
   - dynamic field matches service category/subcategory context.

---

## 6) Services Core
1. Approved business account creates service.
2. Service includes:
   - category/subcategory/city
   - title/description (AR/EN)
   - type (`sale` / `rent`)
   - price + currency
   - address + map coordinates
   - dynamic field values
3. Service status starts as `pending`.
4. Admin approves/rejects.
5. If approved service is edited, it returns to `pending`.

---

## 7) Media (Service Images)
1. Installed Spatie Media Library.
2. Created collections in `Service` model:
   - `main_image` (single)
   - `gallery` (multiple)
3. Store/update handlers save media files.
4. API resource returns image URLs.

---

## 8) Service Browse & Filters
Public APIs provide:
1. Sliders
2. Categories + subcategories
3. Service list/details
4. Filters:
   - city
   - geo radius
   - category/subcategory
   - min/max price
   - service type
   - search text
   - sort options

---

## 9) Favorites, Reports, Reviews
1. Favorites:
   - add/list/remove
2. Reports:
   - report service endpoint
   - admin moderation workflow
3. Reviews:
   - only after accepted request
   - one review per request
   - service stats auto-updated:
     - `average_rating`
     - `review_count`

---

## 10) Service Requests (Orders)
1. Requester chooses approved business account.
2. Sends request with:
   - service id
   - needed time
   - quantity
   - details
   - optional price offer
3. Provider receives in incoming list.
4. Provider can accept/reject.
5. Requester can cancel pending request.

---

## 11) Notifications
1. Database notifications for history (read/unread).
2. API endpoints:
   - list
   - mark read
   - mark all read
3. Admin dashboard notifications UI.
4. Push notifications via FCM (for device users).

---

## 12) Firebase FCM (Push)
1. Create Firebase project.
2. Generate service account JSON.
3. Place JSON in storage (ignored by git).
4. Configure:

```env
FCM_PROJECT_ID=your_project_id
FCM_SERVICE_ACCOUNT_JSON=storage/app/firebase-service-account.json
FCM_V1_ENDPOINT=https://fcm.googleapis.com/v1/projects
```

5. `NotificationService` sends:
   - FCM v1 (preferred)
   - legacy fallback if needed.

Note:
- For dashboard-only notifications, DB + realtime is enough.
- FCM token is needed for mobile/web device push.

---

## 13) Realtime Chat (Pusher)
1. Configure Pusher credentials in `.env`.
2. Use broadcasting config + channels.
3. Conversation/message endpoints.
4. Fire `MessageSent` event on send.
5. Clients subscribe and receive messages instantly.

---

## 14) Sliders
1. Admin CRUD for sliders.
2. Active/scheduled logic.
3. Public API returns active sliders with media.

---

## 15) Profile Update
Implemented endpoint:
- `PUT /api/auth/profile`

Supports:
- name
- phone (unique)
- email (unique, nullable)
- latitude
- longitude
- fcm_token

---

## 16) Postman Documentation Workflow
1. Organized collection by feature folders.
2. Environment variables for tokens and IDs.
3. Auto-save scripts for key IDs/tokens.
4. Added profile update request in auth section.

---

## 17) Testing & QA
Implemented tests for:
- API workflows
- permissions
- reports moderation
- notifications ownership
- chat workflow
- favorites/reports
- OTP security
- profile update
- UltraMsg service behavior

Current full suite status:
- Passing (all tests green in latest run).

---

## 18) Recommended Final Demo Order
1. Register (OTP via WhatsApp UltraMsg)
2. Verify OTP
3. Login
4. Create business account
5. Admin approve business account
6. Create service with media + dynamic fields
7. Admin approve service
8. Browse/filter services
9. Favorite + report
10. Create service request -> accept/reject
11. Add review -> check average rating
12. Realtime chat demo
13. Notification center demo

---

## 19) Key Files You Can Reference
- OTP/UltraMsg:
  - `app/Services/Auth/OtpService.php`
  - `app/Services/WhatsApp/UltraMsgService.php`
- Auth API:
  - `app/Http/Controllers/Api/Auth/AuthController.php`
  - `app/Http/Requests/Api/Auth/*`
- Services:
  - `app/Services/ServiceService.php`
  - `app/Http/Resources/ServiceResource.php`
- Browse:
  - `app/Http/Controllers/Api/ServiceBrowseController.php`
  - `app/Http/Controllers/Api/CategoryBrowseController.php`
  - `app/Http/Controllers/Api/SliderController.php`
- Requests/Reviews:
  - `app/Services/ServiceRequest/ServiceRequestService.php`
  - `app/Services/Review/ReviewService.php`
- Chat:
  - `app/Events/MessageSent.php`
  - `app/Services/Chat/ChatService.php`
- Notifications:
  - `app/Services/Notification/NotificationService.php`

