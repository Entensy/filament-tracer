<?php

return [
    'id' => 'معرف',
    'source' => 'المصدر',
    'error_type' => 'نوع الخطأ',
    'path' => 'المسار',
    'ip' => 'عنوان IP',
    'code' => 'الرمز',
    'message' => 'الرسالة',
    'line' => 'السطر',
    'method' => 'الطريقة',
    'file' => 'الملف',
    'trace' => 'Trace',
    'traces' => 'Traces',
    'caller' => 'المستدعي',
    'queries' => 'الاستعلامات',
    'body' => 'المحتوى',
    'headers' => 'الترويسات',
    'cookies' => 'الكوكيز',
    'created_at' => 'أُنشئ في',
    'when' => 'متى',
    'key' => 'المفتاح',
    'value' => 'القيمة',
    'severity_label' => 'مستوى الخطورة',

    'created_from' => 'منشأ من :date',
    'created_until' => 'حتى :date',

    'navigation' => [
        'group' => 'التشخيص',
        'label' => [
            'singular' => 'Tracer',
            'plural' => 'Tracers',
        ],
    ],

    'sections' => [
        'error' => 'الخطأ',
        'location' => 'الموقع',
        'request' => 'الطلب',
    ],

    'severity' => [
        'critical' => 'حرج',
        'error' => 'خطأ',
        'warning' => 'تحذير',
        'notice' => 'تنبيه',
    ],

    'tabs' => [
        'details' => 'التفاصيل',
        'exceptions' => 'الاستثناءات',
        'queries' => 'الاستعلامات',
        'body' => 'المحتوى',
        'cookies' => 'الكوكيز',
        'headers' => 'الترويسات',
    ],

    'queries' => [
        'connection' => 'الاتصال',
        'time' => 'المدة',
        'bindings' => 'المتغيرات',
    ],

    'placeholders' => [
        'no_traces' => 'لم يُلتقط أي Stack trace لهذا الخطأ.',
        'no_queries' => 'لم يتم تسجيل أي استعلامات قاعدة بيانات خلال هذا الطلب.',
        'no_body' => 'الطلب لا يحتوي على محتوى.',
        'no_headers' => 'لم يتم التقاط أي ترويسات للطلب.',
        'no_cookies' => 'لا توجد كوكيز في هذا الطلب.',
    ],

    'tooltips' => [
        'copy_trace' => 'تم نسخ الـ Trace',
        'copy_path' => 'تم نسخ المسار',
        'copy_caller' => 'تم نسخ المستدعي',
        'copy_sql' => 'تم نسخ استعلام SQL',
        'copy_key' => 'تم نسخ المفتاح',
        'copy_value' => 'تم نسخ القيمة',
        'copy_header' => 'تم نسخ الترويسة',
    ],

    'actions' => [
        'copy_report' => 'نسخ كـ Markdown',
        'copied_title' => 'تم نسخ التقرير',
        'copied_body' => 'تم وضع ملخص Markdown لهذا الـ tracer في الحافظة.',
        'view_tooltip' => 'عرض التفاصيل',
    ],

    'empty_state' => [
        'heading' => 'كل شيء على ما يرام',
        'description' => 'لم يتم التقاط أي أخطاء حتى الآن.',
    ],

    'widget' => [
        'total' => 'إجمالي الـ Tracers',
        'total_description' => 'جميع الأخطاء الملتقطة',
        'today' => 'اليوم',
        'today_description' => 'الأخطاء الملتقطة اليوم',
        'week' => 'آخر 7 أيام',
        'week_description' => 'الاتجاه خلال الأسبوع الماضي',
        'top_error' => 'الخطأ الأكثر حدوثاً',
        'occurrences' => 'مرة',
        'no_errors' => 'لا توجد أخطاء',
    ],

    'empty' => 'فارغ!',
];
