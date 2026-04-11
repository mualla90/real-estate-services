# Submission Checklist

## 1) Code and Database
- [ ] All migrations are committed and runnable.
- [ ] Seeders for minimal demo data are available.
- [ ] `.env.example` includes required keys (DB, queue, mail, Firebase, Pusher).
- [ ] Storage links and media directories are configured.

## 2) Functional Scope
- [ ] Business account workflow (pending/approved/rejected).
- [ ] Service workflow (pending/approved/rejected + revert on edit).
- [ ] Orders/request workflow (incoming/outgoing/accept/reject/delete rules).
- [ ] Reviews only after accepted request.
- [ ] Favorites and reports workflow.
- [ ] Admin report moderation.
- [ ] Notifications (DB + push trigger points).
- [ ] Basic realtime chat works.

## 3) Permissions and Security
- [ ] Admin routes protected by auth + permission middleware.
- [ ] No admin operation is executable without explicit permission.
- [ ] Ownership checks are enforced for notifications/chat/favorites.

## 4) Localization
- [ ] API messages support `en` and `ar`.
- [ ] Dashboard labels/buttons/messages translated in `en` and `ar`.
- [ ] Sidebar entries translated.
- [ ] Dynamic fields/reports/notifications pages translated.

## 5) Testing and Quality
- [ ] Run `php artisan test` and archive passing output.
- [ ] Run smoke checklist and mark pass/fail per step.
- [ ] Verify no PHP syntax errors in modified files.
- [ ] Validate no critical logs/errors during smoke run.

## 6) Documentation Bundle
- [ ] ERD diagram (Drawio/PDF) included.
- [ ] Project requirements document included.
- [ ] Timeline/plan document included.
- [ ] Test coverage map included.
- [ ] QA summary included.
- [ ] Demo smoke checklist included.

## 7) Suggested Evidence for Evaluator
- [ ] Screenshots: admin approve/reject business account.
- [ ] Screenshots: admin approve/reject service.
- [ ] Screenshots: reports moderation + notifications.
- [ ] Screenshots: Arabic and English dashboard pages.
- [ ] Short video: request -> accept -> review flow.

## References
- [implementation-plan-weeks-4-6.md](/d:/Tamkeen/Tasks/real-estate-services/docs/implementation-plan-weeks-4-6.md)
- [test-coverage-map.md](/d:/Tamkeen/Tasks/real-estate-services/docs/test-coverage-map.md)
- [qa-summary-report.md](/d:/Tamkeen/Tasks/real-estate-services/docs/qa-summary-report.md)
- [demo-smoke-checklist.md](/d:/Tamkeen/Tasks/real-estate-services/docs/demo-smoke-checklist.md)
