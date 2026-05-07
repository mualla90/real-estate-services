# Master Test Cases - API (Postman) + Web/Admin

## How to Use This File
1. Run test cases in order.
2. Mark each as:
   - `[ ]` Not run
   - `[x]` Passed
   - `[!]` Failed
3. Record failure reason and endpoint/page.
4. Compare failed items with requirements to find gaps.

---

## A) Environment & Setup Tests

### A-01 App Boot
- Type: Web/API
- Steps:
  1. Start server.
  2. Open app URL.
  3. Call `GET /api/services`.
- Expected:
  - App opens.
  - API responds (not 500).

### A-02 Database Ready
- Type: Web/API
- Steps:
  1. Run migrations + seed.
  2. Open admin login.
  3. Call `GET /api/categories`.
- Expected:
  - No migration errors.
  - Admin page and API work.

### A-03 Locale Switching
- Type: Web/API
- Steps:
  1. Switch web locale via `/lang/ar` then `/lang/en`.
  2. Call API with locale header/query you use.
- Expected:
  - UI labels change.
  - API localized messages are readable.

---

## B) API Test Cases (Postman)

## B-01 Auth & OTP
Use folder `01 Auth`.

### B-01-01 Register
- Request: `POST /api/auth/register`
- Expected:
  - 201
  - user created
  - OTP sent via WhatsApp (if enabled)
  - no `otp_code` leaked in response

### B-01-02 Verify OTP (valid)
- Request: `POST /api/auth/verify-otp`
- Expected:
  - 200/201
  - phone verified

### B-01-03 Verify OTP (wrong code)
- Expected:
  - 422 validation error

### B-01-04 Resend OTP
- Request: `POST /api/auth/resend-otp`
- Expected:
  - success message
  - no `otp_code` in payload

### B-01-05 Login (verified user)
- Request: `POST /api/auth/login`
- Expected:
  - token returned
  - `user_token` saved by Postman script

### B-01-06 Login before verify
- Expected:
  - blocked (validation/business error)

### B-01-07 Me
- Request: `GET /api/auth/me`
- Expected:
  - current user data
  - sensitive fields hidden (`password`, `fcm_token`, etc.)

### B-01-08 Update Profile
- Request: `PUT /api/auth/profile`
- Expected:
  - profile updated
  - unique phone/email enforced

### B-01-09 Logout
- Request: `POST /api/auth/logout`
- Expected:
  - token revoked

---

## B-02 Public Browse
Use folder `02 Public Browse`.

### B-02-01 Sliders
- Request: `GET /api/sliders`
- Expected:
  - active/scheduled sliders only

### B-02-02 Categories
- Request: `GET /api/categories`
- Expected:
  - active categories
  - optional subcategories included

### B-02-03 Subcategories
- Request: `GET /api/subcategories?category_id=...`
- Expected:
  - active subcategories
  - belong to active categories

### B-02-04 Services List
- Request: `GET /api/services`
- Expected:
  - only visible services

### B-02-05 Service Details
- Request: `GET /api/services/{id}`
- Expected:
  - full service payload
  - media URLs present
  - rating fields present

### B-02-06 Service Reviews
- Request: `GET /api/services/{id}/reviews`
- Expected:
  - review list with reviewer info

---

## B-03 Service Filters (Critical)
On `GET /api/services`, test each:
1. `category_id`
2. `subcategory_id`
3. `city_id`
4. `service_type=sale|rent`
5. `min_price`
6. `max_price`
7. `search`
8. `latitude+longitude+radius_km`
9. `sort=latest|price_asc|price_desc|rating_desc`

Expected:
- each filter changes result correctly.

---

## B-04 Business Accounts
Use folder `03 Business Accounts`.

### B-04-01 Create business account
- Expected:
  - status `pending`
  - ID saved to env

### B-04-02 List my business accounts
- Expected:
  - only current user accounts

### B-04-03 Show account details

### B-04-04 Update account

### B-04-05 Unauthorized ownership check
- Use other user token + same account ID
- Expected:
  - 403

---

## B-05 Dynamic Fields
Use folder `04 Dynamic Fields`.

### B-05-01 Fetch by category

### B-05-02 Fetch by category+subcategory

### B-05-03 Invalid subcategory-category relation
- Expected:
  - validation error

---

## B-06 Services CRUD (Business Account)
Use folder `05 Business Services CRUD`.

### B-06-01 Create service (approved BA)
- Expected:
  - created, status pending

### B-06-02 Create service (pending BA)
- Expected:
  - blocked

### B-06-03 Upload main image/gallery
- Expected:
  - URLs returned

### B-06-04 Update service
- Expected:
  - if approved before, goes back to pending

### B-06-05 Delete service

### B-06-06 Dynamic field mismatch
- Expected:
  - blocked by validation

---

## B-07 Service Requests (Orders)
Use folder `06 Service Requests + Reviews`.

### B-07-01 Create request (approved BA)
- Expected:
  - pending request created

### B-07-02 Create duplicate pending request
- Expected:
  - blocked

### B-07-03 Incoming list (provider)

### B-07-04 Outgoing list (requester)

### B-07-05 Accept request

### B-07-06 Reject request

### B-07-07 Cancel pending by requester

### B-07-08 Cancel non-pending
- Expected:
  - blocked

### B-07-09 Request own service
- Expected:
  - blocked

---

## B-08 Reviews

### B-08-01 Add review after accepted request
- Expected:
  - success
  - service rating stats updated

### B-08-02 Add review before accepted
- Expected:
  - blocked

### B-08-03 Add second review for same request
- Expected:
  - blocked

---

## B-09 Favorites
Use folder `07 Favorites + Reports`.

### B-09-01 Add favorite

### B-09-02 List favorites

### B-09-03 Remove favorite

### B-09-04 Ownership protection
- Expected:
  - cannot modify other account favorites

---

## B-10 Reports

### B-10-01 Report visible service

### B-10-02 Report invisible service
- Expected:
  - blocked

---

## B-11 Chat (Realtime)
Use folder `08 Chat`.

### B-11-01 Create conversation

### B-11-02 List conversations

### B-11-03 Send message
- Expected:
  - message saved
  - pusher event emitted

### B-11-04 List messages

### B-11-05 Mark read

### B-11-06 Ownership protection
- Expected:
  - 403 on other account conversation

---

## B-12 Notifications API
Use folder `09 Notifications`.

### B-12-01 List notifications

### B-12-02 Mark one read

### B-12-03 Mark all read

### B-12-04 Ownership protection
- Expected:
  - cannot mark others notifications

---

## C) Web/Admin Test Cases

## C-01 Admin Auth
1. Login valid
2. Login invalid
3. Logout
4. Inactive admin blocked

## C-02 Dashboard
1. Loads without errors
2. Counters/stat cards render
3. Live updates/polling works

## C-03 Permissions Enforcement
1. Admin without permission gets 403
2. Admin with permission can access module
3. Route protection verified per module

## C-04 Roles Management
1. Create role
2. Update role
3. Delete role
4. Assign permissions

## C-05 Admins Management
1. Create admin
2. Update admin
3. Delete admin
4. Role assignment works

## C-06 Activity Types
1. CRUD all actions
2. status toggles if supported

## C-07 Cities
1. CRUD all actions

## C-08 Categories
1. CRUD all actions

## C-09 Subcategories
1. CRUD all actions
2. Parent category relation correct

## C-10 Dynamic Fields
1. Create field category-only
2. Create field subcategory-only
3. Create field category+subcategory
4. Validation: subcategory must match category
5. Edit/Delete field

## C-11 Business Accounts Review
1. List pending
2. Approve
3. Reject (reason)
4. Status updates visible

## C-12 Services Review
1. List pending services
2. Approve service
3. Reject service
4. Activate/deactivate service

## C-13 Reports Moderation
1. List reports
2. Open report details
3. Update report status
4. Permission check on moderation

## C-14 Notifications (Admin Web)
1. List notifications
2. Mark read
3. Mark all read
4. Delete notification (if read rule applies)

## C-15 Sliders
1. Create slider
2. Edit slider
3. Delete slider
4. Schedule/active behavior verified via public API

## C-16 UI/UX Regression
1. RTL/LTR layout
2. Mobile/tablet responsiveness
3. Form validation messages visible
4. Buttons/badges translated
5. Sidebar labels translated

---

## D) Integration Tests (External)

## D-01 UltraMsg OTP
1. Trigger resend OTP.
2. Verify WhatsApp delivery.
3. Verify template variables (`:name`, `:code`).

## D-02 Pusher Chat Realtime
1. Send chat message.
2. Verify event in pusher debug.
3. Verify realtime receive on client.

## D-03 FCM Push
1. Ensure user has valid `fcm_token`.
2. Trigger notification event.
3. Verify push received on device.
4. Verify DB notification still created.

---

## E) Security & Negative Cases
1. Access protected endpoint without token -> unauthorized.
2. Access other user/business-account resource -> forbidden.
3. Invalid media upload type/size -> validation error.
4. OTP brute attempts throttled.
5. Hidden sensitive fields are not leaked in auth responses.

---

## F) Final Release Checklist
1. All API tests pass.
2. All web/admin tests pass.
3. Integrations tested (UltraMsg, Pusher, FCM).
4. Localization checked in EN/AR.
5. Realtime chat verified.
6. Notifications history + push verified.
7. No blocker error in logs.

