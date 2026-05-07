<?php

return [
    'errors' => [
        'unauthorized' => 'غير مصرح.',
        'business_account_not_approved' => 'حساب الأعمال غير مقبول بعد.',
        'service_unavailable' => 'الخدمة غير متاحة.',
        'service_unavailable_for_requests' => 'الخدمة غير متاحة للطلب.',
        'service_unavailable_for_chat' => 'الخدمة غير متاحة للمحادثة.',
        'service_unavailable_for_reporting' => 'الخدمة غير متاحة للإبلاغ.',
        'cannot_request_own_service' => 'لا يمكن طلب خدمتك الخاصة.',
        'cannot_chat_with_self' => 'لا يمكن بدء محادثة مع نفسك.',
        'duplicate_pending_request' => 'لديك طلب قيد الانتظار لهذه الخدمة بالفعل.',
        'only_pending_requests_can_be_cancelled' => 'يمكن إلغاء الطلبات قيد الانتظار فقط.',
        'only_pending_requests_can_be_updated' => 'يمكن تعديل الطلبات قيد الانتظار فقط.',
        'review_requires_accepted_request' => 'يمكن إضافة تقييم للطلبات المقبولة فقط.',
        'request_already_reviewed' => 'تم تقييم طلب الخدمة هذا مسبقًا.',
        'favorite_not_found' => 'العنصر غير موجود في المفضلة.',
        'invalid_report_status' => 'حالة البلاغ غير صالحة.',
        'only_pending_services_can_be_approved' => 'يمكن قبول الخدمات قيد الانتظار فقط.',
        'only_pending_services_can_be_rejected' => 'يمكن رفض الخدمات قيد الانتظار فقط.',
    ],

    'auth' => [
        'registered_otp_sent' => 'تم التسجيل بنجاح. تم إرسال رمز التحقق.',
        'login_otp_sent' => 'تم التحقق من بيانات الدخول. تم إرسال رمز التحقق.',
        'login_successful' => 'تم تسجيل الدخول بنجاح.',
        'phone_verified' => 'تم التحقق من رقم الهاتف بنجاح.',
        'otp_resent' => 'تمت إعادة إرسال رمز التحقق بنجاح.',
        'logged_out' => 'تم تسجيل الخروج.',
        'profile_updated' => 'تم تحديث الملف الشخصي بنجاح.',
    ],

    'business_accounts' => [
        'fetched' => 'تم جلب حسابات الأعمال بنجاح.',
        'single_fetched' => 'تم جلب حساب الأعمال بنجاح.',
        'created' => 'تم إنشاء حساب الأعمال بنجاح.',
        'updated' => 'تم تحديث حساب الأعمال بنجاح.',
    ],

    'services' => [
        'fetched' => 'تم جلب الخدمات بنجاح.',
        'single_fetched' => 'تم جلب الخدمة بنجاح.',
        'created' => 'تم إنشاء الخدمة بنجاح.',
        'updated' => 'تم تحديث الخدمة بنجاح.',
        'deleted' => 'تم حذف الخدمة بنجاح.',
    ],

    'sliders' => [
        'fetched' => 'تم جلب الشرائح الإعلانية بنجاح.',
    ],

    'categories' => [
        'fetched' => 'تم جلب التصنيفات بنجاح.',
    ],

    'subcategories' => [
        'fetched' => 'تم جلب التصنيفات الفرعية بنجاح.',
    ],

    'cities' => [
        'fetched' => 'تم جلب المدن بنجاح.',
    ],

    'activity_types' => [
        'fetched' => 'تم جلب أنواع النشاط بنجاح.',
    ],

    'dynamic_fields' => [
        'fetched' => 'تم جلب الحقول الديناميكية بنجاح.',
    ],

    'content' => [
        'fetched' => 'تم جلب صفحة المحتوى بنجاح.',
        'privacy_policy' => [
            'title' => 'سياسة الخصوصية',
            'body' => 'نقوم بجمع المعلومات اللازمة لإنشاء الحسابات، والتحقق من حسابات الأعمال، ونشر الخدمات، ومعالجة طلبات الخدمات، ودعم المفضلة والبلاغات والإشعارات والمحادثات. نستخدم هذه المعلومات فقط لتشغيل المنصة وتحسينها وحماية المستخدمين والامتثال للمتطلبات القانونية.',
        ],
        'terms_of_use' => [
            'title' => 'شروط الاستخدام',
            'body' => 'باستخدام المنصة، فإنك توافق على تقديم معلومات صحيحة، واستخدام حسابات أعمال معتمدة للنشاط التجاري، واحترام المستخدمين الآخرين، وعدم نشر محتوى مضلل أو غير قانوني أو ضار. يحق للإدارة مراجعة حسابات الأعمال والخدمات وقبولها أو رفضها أو إيقافها أو حذفها.',
        ],
    ],

    'favorites' => [
        'fetched' => 'تم جلب المفضلة بنجاح.',
        'added' => 'تمت إضافة الخدمة إلى المفضلة بنجاح.',
        'removed' => 'تمت إزالة الخدمة من المفضلة بنجاح.',
    ],

    'reports' => [
        'submitted' => 'تم إرسال البلاغ بنجاح.',
    ],

    'reviews' => [
        'fetched' => 'تم جلب التقييمات بنجاح.',
        'created' => 'تمت إضافة التقييم بنجاح.',
    ],

    'notifications' => [
        'fetched' => 'تم جلب الإشعارات بنجاح.',
        'marked_read' => 'تم تعليم الإشعار كمقروء.',
        'marked_all_read' => 'تم تعليم جميع الإشعارات كمقروءة.',
    ],

    'notification_text' => [
        'business_account_pending_review_title' => 'حساب أعمال جديد قيد المراجعة',
        'business_account_pending_review_message' => 'يوجد حساب أعمال جديد بانتظار الموافقة.',
        'business_account_approved_title' => 'تم قبول حساب الأعمال',
        'business_account_approved_message' => 'تمت الموافقة على حساب الأعمال الخاص بك.',
        'business_account_rejected_title' => 'تم رفض حساب الأعمال',
        'business_account_rejected_message' => 'تم رفض حساب الأعمال الخاص بك.',
        'service_pending_review_title' => 'خدمة جديدة قيد المراجعة',
        'service_pending_review_message' => 'توجد خدمة جديدة بانتظار الموافقة.',
        'service_approved_title' => 'تم قبول الخدمة',
        'service_approved_message' => 'تمت الموافقة على خدمتك ونشرها.',
        'service_rejected_title' => 'تم رفض الخدمة',
        'service_rejected_message' => 'تم رفض خدمتك من قبل الإدارة.',
        'new_service_request_title' => 'طلب خدمة جديد',
        'new_service_request_message' => 'لقد وصلك طلب خدمة جديد.',
    ],

    'chat' => [
        'conversations_fetched' => 'تم جلب المحادثات بنجاح.',
        'conversation_created' => 'تم إنشاء المحادثة بنجاح.',
        'messages_fetched' => 'تم جلب الرسائل بنجاح.',
        'message_sent' => 'تم إرسال الرسالة بنجاح.',
        'messages_marked_read' => 'تم تعليم الرسائل كمقروءة.',
    ],

    'service_requests' => [
        'outgoing_fetched' => 'تم جلب الطلبات المرسلة بنجاح.',
        'incoming_fetched' => 'تم جلب الطلبات المستلمة بنجاح.',
        'created' => 'تم إنشاء طلب الخدمة بنجاح.',
        'accepted' => 'تم قبول طلب الخدمة بنجاح.',
        'rejected' => 'تم رفض طلب الخدمة بنجاح.',
        'cancelled' => 'تم إلغاء طلب الخدمة بنجاح.',
    ],
];
