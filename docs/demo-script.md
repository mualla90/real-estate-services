# Demo Script (Ready to Present)

## Duration
- Total: 12-15 minutes
- Language: start in English, switch to Arabic near the end

## Setup (Before Recording)
1. Start app and confirm login works for:
- Admin account
- User A (service owner)
- User B (requester)
2. Ensure at least one approved business account exists for each user.
3. Keep these pages ready in tabs:
- Admin dashboard
- Business accounts review
- Services review
- Reports
- Notifications

## 0:00-1:00 Project Intro
1. Explain system modules:
- Laravel API
- Admin Dashboard (Blade)
- Roles and permissions
- Arabic/English support
- Notifications and basic realtime chat

Expected result:
- Evaluator understands architecture and scope quickly.

## 1:00-3:00 Business Account Lifecycle
1. As User A, create a new business account (pending).
2. Open Admin -> Business Accounts.
3. Approve one pending account.
4. Reject another pending account with reason.

Expected result:
- Status changes: `pending -> approved/rejected`.
- Rejection reason stored.
- Notification generated for owner.

Evidence:
- Screenshot of pending list.
- Screenshot after approve/reject.

## 3:00-5:30 Service Lifecycle + Review Rules
1. As approved business account, create a service with:
- Category/subcategory
- Price/type
- Photos (media)
- Dynamic fields
2. Show service enters `pending`.
3. Admin approves service.
4. Edit approved service as owner.
5. Show it automatically returns to `pending`.

Expected result:
- Approval workflow enforced.
- Auto-revert to pending on edit works.

Evidence:
- Screenshot service pending.
- Screenshot approved state.
- Screenshot after edit returning to pending.

## 5:30-7:30 Service Requests + Reviews
1. As User B, create a service request to User A service.
2. As User A, open incoming requests and accept one.
3. As User B, submit review for accepted request.
4. Attempt second review for same request.

Expected result:
- Review allowed only for accepted request.
- Duplicate review blocked.

Evidence:
- Screenshot accepted request.
- Screenshot successful review.
- Screenshot duplicate prevention message.

## 7:30-9:00 Favorites + Reports
1. Add service to favorites.
2. List favorites.
3. Remove favorite.
4. Submit report on service.
5. Admin opens reports and updates report status.

Expected result:
- Favorites CRUD works.
- Report moderation works with status updates.

Evidence:
- Screenshot favorites list.
- Screenshot report details/status update.

## 9:00-10:30 Notifications
1. Open Admin notifications list.
2. Mark notification as read.
3. Mark all as read.
4. Delete a read notification.
5. Show unread notification cannot be deleted.

Expected result:
- Read/unread rules enforced.
- Delete only allowed for read notifications.

Evidence:
- Screenshot before and after read actions.
- Screenshot delete restriction for unread.

## 10:30-12:00 Chat (Basic Realtime)
1. Create conversation from service details.
2. Send text message from one side.
3. Show message received on other side.
4. Mark message as read.

Expected result:
- Basic realtime chat works.
- Read state updates correctly.

Evidence:
- Screenshot conversation list.
- Screenshot sent/received/read states.

## 12:00-13:30 Permissions Demo
1. Login with limited admin role.
2. Try protected action (approve service or manage report).

Expected result:
- Access denied (`403`) without permission.

Evidence:
- Screenshot denied action.

## 13:30-15:00 i18n Switch (English/Arabic)
1. Show dashboard in English.
2. Switch to Arabic.
3. Open dynamic fields, reports, notifications, sidebar.

Expected result:
- No raw translation keys.
- Labels/buttons/messages appear correctly in both languages.

Evidence:
- Screenshot same page in EN and AR.

## Final Closing Line
- "The platform now covers full lifecycle workflows (accounts, services, requests), enforces role-based permissions, supports bilingual UI/API messaging, and passes automated feature tests."

## Backup Commands (If Asked Live)
```bash
php artisan test
php artisan test tests/Feature/ApiWorkflowTest.php
php artisan test tests/Feature/AdminPermissionsTest.php
```
