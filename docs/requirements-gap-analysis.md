# Requirements Gap Analysis (FR-01 .. FR-43)

## Legend
- `Done`: منفذ وموجود في الكود.
- `Partial`: موجود جزئيا ويحتاج استكمال.
- `Missing`: غير منفذ بعد.

## FR Matrix
| FR | المتطلب | الحالة | ملاحظات |
|---|---|---|---|
| FR-01 | إنشاء حساب مستخدم | Done | API Register موجود مع OTP. |
| FR-02 | تسجيل دخول مستخدم | Done | API Login عبر phone/password + token. |
| FR-03 | تسجيل خروج مستخدم | Done | Logout عبر revoke token. |
| FR-04 | تعديل الملف الشخصي | Missing | لا يوجد endpoint/profile flow واضح. |
| FR-05 | إضافة حساب أعمال | Done | API store business account. |
| FR-06 | تعديل حساب أعمال | Done | API update business account. |
| FR-07 | عرض حسابات الأعمال | Done | API index/show لمالك الحساب. |
| FR-08 | قبول حساب أعمال | Done | Admin approve موجود. |
| FR-09 | رفض حساب أعمال | Done | Admin reject موجود. |
| FR-10 | إضافة خدمة | Done | مشروط بوجود business account approved. |
| FR-11 | تعديل خدمة | Done | API update service موجود. |
| FR-12 | حذف خدمة | Done | API delete service موجود. |
| FR-13 | عرض الخدمات | Partial | موجود عرض خدمات ضمن business account، ينقص listing عام مع فلاتر متقدمة. |
| FR-14 | قبول خدمة | Done | Admin approve service موجود. |
| FR-15 | رفض خدمة | Done | Admin reject service موجود. |
| FR-16 | إضافة تصنيف | Done | Admin categories CRUD. |
| FR-17 | تعديل تصنيف | Done | Admin categories CRUD. |
| FR-18 | حذف تصنيف | Done | Admin categories CRUD. |
| FR-19 | إضافة تصنيف فرعي | Done | Admin subcategories CRUD. |
| FR-20 | تعديل تصنيف فرعي | Done | Admin subcategories CRUD. |
| FR-21 | حذف تصنيف فرعي | Done | Admin subcategories CRUD. |
| FR-22 | إضافة حقول ديناميكية | Missing | جدول/CRUD غير موجود في migrations/routes. |
| FR-23 | تعديل حقول ديناميكية | Missing | غير موجود. |
| FR-24 | حذف حقول ديناميكية | Missing | غير موجود. |
| FR-25 | إضافة طلب خدمة | Missing | service_requests غير منفذة. |
| FR-26 | عرض الطلبات المستلمة | Missing | غير موجود. |
| FR-27 | عرض الطلبات المرسلة | Missing | غير موجود. |
| FR-28 | قبول طلب خدمة | Missing | غير موجود. |
| FR-29 | رفض طلب خدمة | Missing | غير موجود. |
| FR-30 | حذف طلب خدمة | Missing | غير موجود. |
| FR-31 | إضافة تقييم | Missing | reviews غير منفذة. |
| FR-32 | إضافة خدمة للمفضلة | Missing | favorites غير منفذة. |
| FR-33 | حذف خدمة من المفضلة | Missing | غير موجود. |
| FR-34 | الإبلاغ عن خدمة | Missing | reports غير منفذة. |
| FR-35 | إدارة السلايدر الإعلاني | Missing | sliders غير منفذة. |
| FR-36 | إضافة مدينة | Done | Admin cities CRUD. |
| FR-37 | إدارة البلاغات | Missing | reports admin workflow غير موجود. |
| FR-38 | إضافة دور | Done | Admin roles CRUD موجود. |
| FR-39 | تعديل دور | Done | موجود. |
| FR-40 | حذف دور | Done | موجود. |
| FR-41 | تحديد صلاحيات للدور | Done | syncPermissions موجودة في الدور. |
| FR-42 | إضافة مدير جديد | Done | Admin management موجود. |
| FR-43 | تعديل صلاحيات مدير | Done | تعديل أدوار/صلاحيات المدير موجود عبر admin module. |

## Cross-Cutting Checks
- الصلاحيات: `Done` على مسارات لوحة التحكم الأساسية عبر `permission:*` middleware.
- تعدد اللغات: `Partial`.
  - موجود في النماذج والـ resources الأساسية (translatable + locale middleware).
  - يحتاج توحيد كامل لرسائل API والتنبيهات وواجهات dashboard.
- دورة الحياة:
  - Business Accounts: `Done` (pending/approved/rejected).
  - Services: `Done` (pending/approved/rejected + reset to pending on update).
  - Orders: `Missing` بالكامل.

## Risks الحالية
- لا توجد اختبارات وظيفية تغطي المتطلبات الأساسية (Feature tests شبه فارغة).
- أجزاء كبيرة من ERD ما زالت غير منفذة (orders, reviews, favorites, reports, dynamic fields, sliders, notifications, chat).
- README/Docs المشروع لا يعكس المتطلبات الحالية.

## توصية التنفيذ القادمة
1. تنفيذ FR-25..FR-31 أولا (orders + reviews) لأنها قلب دورة العمل.
2. ثم FR-32..FR-37 (favorites/reports/sliders + admin report management).
3. بعدها Firebase notifications و Pusher chat.
