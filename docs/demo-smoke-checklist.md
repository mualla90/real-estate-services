# Demo Smoke Checklist

Use this checklist before final demo/submission to verify critical workflows quickly (Arabic/English + Admin/API behavior).

## Preconditions
- Run migrations and seeders.
- At least one admin account exists with full review/manage permissions.
- At least two user accounts exist, each with an approved business account.
- App locale switch works (`en`/`ar`).

## 1) Business Account Review Flow
1. User creates a new business account via API/mobile.
2. Confirm initial status = `pending`.
3. Admin opens business accounts review screen.
4. Admin approves one account; reject another with reason.
5. Verify user receives notification for approve/reject.
6. Verify rejected reason appears in details.

Expected: lifecycle is correct, only admin with permission can approve/reject.

## 2) Service Flow + Media + Taxonomy
1. Approved business account creates a service.
2. Attach media (main photo + additional photos) through Media Library flow.
3. Set category + optional subcategory.
4. Fill dynamic fields for selected category/subcategory.
5. Confirm service status starts as `pending`.
6. Admin approves service.
7. Edit approved service as owner.
8. Confirm status returns to `pending` after edit.

Expected: create/edit rules enforced, review lifecycle correct.

## 3) Browse + Filters
1. Open services listing endpoint/page.
2. Filter by category/subcategory.
3. Filter by service type (`sale`/`rent`).
4. Filter by price min/max.
5. Search by title.

Expected: filtered results are correct and stable.

## 4) Requests + Reviews
1. Requester creates service request.
2. Provider views incoming requests.
3. Provider accepts one request and rejects another.
4. Requester adds review only for accepted request.
5. Try duplicate review for same request.

Expected: accepted-only review rule enforced; duplicate blocked.

## 5) Favorites + Reports
1. Add service to favorites.
2. List favorites.
3. Remove favorite.
4. Submit report on visible service.
5. Admin opens reports screen and updates report status.

Expected: ownership checks and status transitions work.

## 6) Notifications (Database + Push)
1. Trigger events: business account reviewed, service reviewed, new request.
2. Verify push is sent to user device token (Firebase).
3. Verify notification appears in DB notifications list/history.
4. Admin marks notification read.
5. Admin deletes a read notification.
6. Try deleting unread notification.

Expected: unread delete is blocked; read delete allowed.

## 7) Chat Basic Realtime
1. Create conversation from service details.
2. Send text message.
3. Verify broadcast arrives in realtime.
4. Mark message read.
5. Ensure non-participant cannot access conversation/messages.

Expected: basic realtime + authorization rules pass.

## 8) i18n Validation (`en` and `ar`)
1. Switch dashboard language to English; verify labels/buttons/messages.
2. Switch to Arabic; verify labels/buttons/messages.
3. Confirm dynamic fields and reports pages fully translated.
4. Confirm notification messages are translated.
5. Confirm sidebar entries are translated.

Expected: no raw translation keys appear.

## 9) Permissions Audit Smoke
1. Login with limited admin role.
2. Attempt protected actions without permission:
   - approve/reject service
   - approve/reject business account
   - manage reports
   - manage notifications
3. Confirm responses are denied (`403`) and UI hides actions where needed.

Expected: no sensitive admin operation is reachable without explicit permission.

## 10) Final Commands
```bash
php -l lang/ar/admin.php
php artisan test
```

Expected: syntax check passes and feature tests pass.
