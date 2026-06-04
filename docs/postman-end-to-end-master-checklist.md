# Postman End-to-End Master Checklist (From Zero)

## Goal
Run and verify the full project flow from start to finish using Postman, without missing any requirement.

---

## 0) Prerequisites (Before Postman)
1. Install dependencies and run app.
2. Configure `.env`:
   - DB
   - `APP_URL`
   - Passport/auth settings
   - Pusher
   - Firebase (if testing push)
   - UltraMsg (if testing WhatsApp OTP)
3. Run migrations + seed:
   - `php artisan migrate:fresh --seed`
   - (Optional) `php artisan db:seed --class=RealEstateDemoSeeder`
4. Clear caches:
   - `php artisan optimize:clear`
5. Ensure server is running:
   - `php artisan serve`

---

## 1) Import Postman Assets
1. Import collection:
   - `docs/postman/real-estate-services.cleaned.postman_collection.json`
2. Import the local environment:
   - `docs/postman/real-estate-services.local.postman_environment.json`
3. Import the server environment:
   - `docs/postman/real-estate-services.server.postman_environment.json`
4. Select environment:
   - `Real Estate Services Local`
   - or `Real Estate Services Server`
5. Confirm variables:
   - `base_url=http://127.0.0.1:8000/api`
   - or `base_url=https://your-domain.com/api`
   - IDs for `activity_type_id`, `category_id`, `city_id`
   - `user_token` empty initially

---

## 2) Execution Order (Main Scenario)

## 2.1 Auth (User)
Folder: `01 Auth`
1. `Register`
2. `Verify OTP`
   - If UltraMsg enabled: use code from WhatsApp
3. `Login (Save user_token)`
4. `Me`
5. `Update Profile`
6. `Logout` (optional now, usually do later)

Expected:
- `user_token` stored
- no OTP code in response payload
- profile updated successfully

---

## 2.2 Public Browse (No auth required for list/detail)
Folder: `02 Public Browse`
1. `List Categories (with subcategories)`
2. `List Subcategories (optional by category)`
3. `Sliders`
4. `List Services (filters example)`
5. `Service Details`
6. `Service Reviews`

Expected:
- categories/subcategories localized
- sliders active/scheduled only
- filters work (search, type, price, category, subcategory, city, geo)

---

## 2.3 Business Account Lifecycle
Folder: `03 Business Accounts`
1. `Login` (if token expired)
2. `Create Business Account (Save business_account_id)`
3. `My Business Accounts`
4. `Business Account Details`
5. `Update Business Account`

Expected:
- account created in `pending`
- `business_account_id` saved in env

Admin checkpoint (Dashboard, not Postman):
- Approve this business account from admin panel.

---

## 2.4 Dynamic Fields Fetch by Context
Folder: `04 Dynamic Fields`
1. `List Dynamic Fields By Category` with `category_id`
2. Repeat with `subcategory_id` (optional)

Expected:
- only matching active dynamic fields returned
- category/subcategory context enforced

---

## 2.5 Service CRUD + Media
Folder: `05 Business Services CRUD`
1. `Create Service (Save business_service_id + target_service_id)`
   - enable `main_image` and/or `images[0]` for file upload test
2. `List Business Services`
3. `Business Service Details`
4. `Update Service`
5. `Delete Service` (do this at end only)

Expected:
- create blocked if business account not approved
- service initially `pending`
- if approved then updated => returns to `pending`
- media URLs appear in response

Admin checkpoint (Dashboard, not Postman):
- Approve the created service before browse/request tests.

---

## 2.6 Service Requests (Orders)
Folder: `06 Service Requests + Reviews`
Precondition:
- requester has approved business account
- target service is visible (approved + active + published)

1. `Create Service Request (Save service_request_id)`
2. `Outgoing Requests`
3. `Incoming Requests` (provider account context)
4. `Accept Service Request` (provider)
5. `Reject Service Request` (alternative path)
6. `Delete Outgoing Request` (only pending/cancellable path)
7. `Add Review To Accepted Request`

Expected:
- cannot request own service
- no duplicate pending request for same pair
- review only after accepted request
- one review per service request

---

## 2.7 Favorites + Reports
Folder: `07 Favorites + Reports`
1. `Add Favorite`
2. `Favorites List`
3. `Remove Favorite`
4. `Report Service`

Expected:
- ownership checks enforced
- report stored and visible for admin moderation

---

## 2.8 Chat (Realtime Core)
Folder: `08 Chat`
1. `Create Conversation (Save conversation_id)`
2. `Conversations List`
3. `Send Message`
4. `Conversation Messages`
5. `Mark Conversation Read`

Expected:
- only account owner can access
- message saved and broadcast event fired
- read status updates for receiver-side messages

---

## 2.9 Notifications
Folder: `09 Notifications`
1. `List Notifications (Save first notification_id)`
2. `Mark Notification Read`
3. `Mark All Notifications Read`

Expected:
- only owner can read/update own notifications
- unread/read status behaves correctly

---

## 3) Admin-Side Scenario Checkpoints (Dashboard)
These are required to complete lifecycle scenarios correctly:
1. Approve/reject business accounts
2. Approve/reject services
3. Manage reports (status moderation)
4. Review notifications
5. Verify role/permission restrictions
6. Verify sliders management
7. Verify dynamic fields CRUD and category/subcategory linking

---

## 4) Requirement-to-Endpoint Coverage
1. Auth + OTP + profile:
   - `/auth/register`, `/auth/verify-otp`, `/auth/login`, `/auth/me`, `/auth/profile`
2. Browse:
   - `/categories`, `/subcategories`, `/sliders`, `/services`, `/services/{id}`
3. Business accounts:
   - `/business-accounts` CRUD
4. Services:
   - `/business-accounts/{id}/services` CRUD
5. Dynamic fields:
   - `/dynamic-fields`
6. Requests:
   - `/business-accounts/{id}/service-requests/*`
7. Reviews:
   - `/services/{id}/reviews` and create review endpoint
8. Favorites:
   - `/business-accounts/{id}/favorites/*`
9. Reports:
   - `/business-accounts/{id}/reports/services/{service}`
10. Chat:
   - `/business-accounts/{id}/conversations/*`
11. Notifications:
   - `/notifications`, `/notifications/{id}/read`, `/notifications/read-all`

---

## 5) Data/State Validation Checklist
After each major step, validate DB/state:
1. User:
   - `phone_verified_at`, `last_login_at`, profile fields
2. Business account:
   - status transitions (`pending/approved/rejected`)
3. Service:
   - status transitions and media records
4. Service request:
   - pending -> accepted/rejected/cancelled
5. Review:
   - only one per request
   - service `average_rating` + `review_count` updated
6. Favorite/report:
   - ownership and visibility rules
7. Notifications:
   - read/unread markers
8. Chat:
   - sent/read state and event behavior

---

## 6) Failure/Negative Test Cases (Must Run)
1. Login before phone verification -> blocked
2. Resend OTP for unknown phone -> validation error
3. Create service with pending business account -> blocked
4. Dynamic field not matching service category/subcategory -> blocked
5. Request own service -> blocked
6. Add review before request accepted -> blocked
7. Mark another user’s notification as read -> forbidden
8. Access another account favorites/requests/chat -> forbidden

---

## 7) Final Demo Run (Suggested)
1. Register + WhatsApp OTP verify
2. Login + profile update
3. Create business account
4. Admin approves account
5. Create service with images/dynamic fields
6. Admin approves service
7. Browse/filter service
8. Favorite + report
9. Create service request + accept
10. Add review and show updated rating
11. Send chat message
12. Show notifications list + mark read

---

## 8) Notes
1. Currency currently stored as single `price` + single `currency`.
2. One service row supports one currency at a time.
3. For both USD/SYP simultaneously, schema change is required.
4. OTP in responses is intentionally removed for security.
