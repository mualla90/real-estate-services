# Test Coverage Map (FR -> Automated Tests)

## Scope
This map links current functional requirements (FR) to automated feature tests in `tests/Feature`.

## Covered by Automated Tests
| FR | Requirement | Test Coverage |
|---|---|---|
| FR-10 | Add Service | `ApiWorkflowTest::test_pending_business_account_cannot_create_service` (business account approval gate) |
| FR-11 | Edit Service | `ApiWorkflowTest::test_updating_approved_service_moves_it_back_to_pending` |
| FR-25 | Create Service Request | Indirect setup/usage in `ApiWorkflowTest` (review flow prerequisites) |
| FR-28/FR-29 | Accept/Reject Service Request Rules | `ApiWorkflowTest::test_review_requires_accepted_service_request` (only accepted can be reviewed) |
| FR-31 | Add Review | `ApiWorkflowTest::test_service_request_can_be_reviewed_only_once` + accepted-only rule test |
| FR-32/FR-33 | Favorites add/remove/list | `FavoritesReportsWorkflowTest::test_can_add_list_and_remove_favorite` + ownership guard test |
| FR-34 | Report Service | `FavoritesReportsWorkflowTest::test_can_submit_report_for_visible_service` + invisible-service guard test |
| FR-08/FR-09 | Approve/Reject Business Account (admin action + side-effects) | `AdminLifecycleNotificationsTest::test_business_account_approve_and_reject_update_status_and_notify_owner` |
| FR-14/FR-15 | Approve/Reject Service (admin action + side-effects) | `AdminLifecycleNotificationsTest::test_service_approve_reject_activate_deactivate_and_notify_owner` |
| FR-37 | Admin report moderation | `AdminReportModerationTest` (permission + status transition + reviewed fields) |
| FR-38..FR-43 | Roles/Permissions enforcement (admin operations) | `AdminPermissionsTest` (`403` without permission, allow with permission) |
| FR-08/FR-14/FR-15 (permission side) | Admin protected actions cannot be accessed without permission | `AdminPermissionsTest::test_admin_without_permissions_gets_403_on_protected_routes` |
| Chat feature (project stage 5) | Conversation/message/read/authorization | `ChatWorkflowTest` (4 tests) |

## Test Files Summary
- `tests/Feature/ApiWorkflowTest.php`
  - `test_pending_business_account_cannot_create_service`
  - `test_updating_approved_service_moves_it_back_to_pending`
  - `test_review_requires_accepted_service_request`
  - `test_service_request_can_be_reviewed_only_once`
- `tests/Feature/AdminPermissionsTest.php`
  - `test_guest_is_redirected_to_admin_login_on_protected_route`
  - `test_admin_without_permissions_gets_403_on_protected_routes`
  - `test_admin_with_reports_view_permission_can_access_reports_index`
  - `test_admin_with_notifications_manage_permission_can_mark_all_read`
  - `test_admin_with_services_view_permission_can_access_service_review_index`
- `tests/Feature/ChatWorkflowTest.php`
  - `test_can_create_conversation_for_visible_service`
  - `test_can_send_message_in_conversation`
  - `test_mark_read_updates_only_received_sent_messages`
  - `test_non_owner_cannot_access_other_business_account_conversations`
- `tests/Feature/FavoritesReportsWorkflowTest.php`
  - `test_can_add_list_and_remove_favorite`
  - `test_cannot_manage_favorites_for_another_business_account`
  - `test_can_submit_report_for_visible_service`
  - `test_cannot_submit_report_for_invisible_service`
- `tests/Feature/AdminLifecycleNotificationsTest.php`
  - `test_business_account_approve_and_reject_update_status_and_notify_owner`
  - `test_service_approve_reject_activate_deactivate_and_notify_owner`
- `tests/Feature/AdminReportModerationTest.php`
  - `test_admin_without_reports_manage_cannot_update_report_status`
  - `test_admin_with_reports_manage_can_update_report_status_and_review_fields`
- `tests/Feature/NotificationOwnershipTest.php`
  - `test_api_user_cannot_mark_read_notification_that_belongs_to_another_user`
  - `test_admin_cannot_delete_unread_notification_but_can_delete_read_notification`
  - `test_admin_cannot_mark_or_delete_notification_owned_by_other_admin`

## Not Yet Covered (Recommended Next)
1. FR-05..FR-09: Full business account lifecycle (API + admin approve/reject) with assertions on notification side-effects.
2. FR-12/FR-13: Service delete/browse filters (search, category, price, location/radius).
3. FR-16..FR-24/FR-36/FR-37: CRUD tests for taxonomy, dynamic fields, cities, and reports moderation.
4. FR-05..FR-07: deeper business-account API lifecycle scenarios and filtering coverage.
5. FR-13: advanced public services browse filters (location radius, price bounds, sort combinations).

## Run Commands
```bash
php artisan test
php artisan test tests/Feature/ApiWorkflowTest.php
php artisan test tests/Feature/AdminPermissionsTest.php
php artisan test tests/Feature/ChatWorkflowTest.php
php artisan test tests/Feature/FavoritesReportsWorkflowTest.php
php artisan test tests/Feature/AdminLifecycleNotificationsTest.php
php artisan test tests/Feature/AdminReportModerationTest.php
php artisan test tests/Feature/NotificationOwnershipTest.php
```
