# Implementation Plan (Weeks 4-6)

## الهدف
إكمال المتطلبات غير المنفذة في 3 مراحل عملية مع الحفاظ على الاستقرار والصلاحيات ودعم اللغتين.

## Week 4: Orders + Reviews + Notifications Core

### Scope
- FR-25..FR-30: Service Requests كامل.
- FR-31: Reviews بعد قبول الطلب فقط.
- قاعدة Notifications داخل النظام + تجهيز device token flow.

### Deliverables
1. جداول وموديلات:
- `service_requests`
- `reviews`
- `notifications` (custom/domain or Laravel notifications schema حسب اختيارنا)

2. API endpoints:
- إنشاء طلب خدمة.
- عرض الطلبات المرسلة/المستلمة.
- قبول/رفض الطلب.
- حذف الطلب (المرسل فقط، ضمن القواعد).
- إضافة تقييم مع تحقق قواعد الأهلية.

3. قواعد العمل:
- لا طلب بدون business account approved.
- المستلم فقط يقبل/يرفض الطلب.
- التقييم مسموح فقط إذا الطلب accepted/completed حسب السياسة المعتمدة.
- تحديث `average_rating` و `review_count` بشكل transaction-safe.

4. Dashboard:
- شاشة متابعة طلبات (قراءة + حالات) مبدئيا.

5. اختبارات:
- Feature tests لكل transitions الرئيسية.

## Week 5: Chat (3 days) + Firebase Push

### Scope
- Chat أساسي فقط.
- Push notifications عبر Firebase للأحداث الحرجة.

### Deliverables
1. Chat schema:
- `conversations`
- `messages`

2. API:
- إنشاء conversation من صفحة الخدمة.
- إرسال رسالة نصية.
- جلب محادثات المستخدم + رسائل محادثة.
- تحديث حالة الرسالة (`sent` / `read`).

3. Realtime:
- Broadcast events عبر Pusher.
- استقبال الرسائل فوريا على الموبايل.

4. Push عبر Firebase:
- حفظ/تحديث `device_token` للمستخدم.
- إرسال push عند:
  - قبول حساب أعمال.
  - قبول خدمة.
  - وصول طلب جديد.

5. اختبارات:
- اختبارات policy/authorization لعدم اختراق المحادثات.

## Week 6: Favorites + Reports + Sliders + Hardening

### Scope
- FR-32..FR-37 + تحسينات نهائية.

### Deliverables
1. Features:
- Favorites add/remove/list.
- Reports create + admin moderation panel.
- Sliders CRUD في dashboard + API read endpoint.

2. Advanced filters (services listing):
- location, category/subcategory, min/max price, service_type, text search.

3. i18n hardening:
- توحيد الترجمات في:
  - رسائل API
  - تنبيهات push
  - dashboard labels/validation

4. Security and permissions audit:
- مراجعة كل route/action للتأكد من middleware/policy.
- منع أي admin action بدون permission صريحة.

5. Quality gates:
- Feature tests للـ critical flows.
- معالجة N+1 في endpoints عالية الاستخدام.
- توثيق API endpoints النهائية.

## ترتيب التنفيذ الفني المقترح
1. Database-first (migrations + foreign keys + indexes).
2. Models/Relations/Policies.
3. Services layer (business rules + transactions).
4. Controllers + Requests validation.
5. Resources/transformers + i18n messages.
6. Dashboard screens.
7. Automated tests.

## Definition of Done
- كل FR ضمن النطاق له endpoint/واجهة تعمل.
- الصلاحيات مطبقة ومختبرة.
- رسائل النظام بالعربي والإنجليزي.
- لا أخطاء منطقية في lifecycle transitions.
- اختبارات feature الأساسية تمر بنجاح.

## Next Build Slice (مباشر)
- أول slice للتنفيذ الفوري:
1. إنشاء `service_requests` migration/model.
2. API: create/list incoming/list outgoing.
3. API: accept/reject/delete.
4. Feature tests لسيناريو كامل طلب.
