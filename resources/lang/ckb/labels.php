<?php

return [
    'id' => 'ژمارە',
    'source' => 'سەرچاوە',
    'error_type' => 'جۆری ئیرۆر',
    'path' => 'ڕێڕەو',
    'ip' => 'ئایپی ئەدرێس',
    'code' => 'کۆد',
    'message' => 'پەیام',
    'line' => 'هێڵ',
    'method' => 'کردار',
    'file' => 'فایل',
    'trace' => 'Trace',
    'traces' => 'Traces',
    'caller' => 'پەیوەندیکەر',
    'queries' => 'Queries',
    'body' => 'ناوەڕۆک',
    'headers' => 'ناونیشانەکان',
    'cookies' => 'Cookies',
    'created_at' => 'دروستکراوە لە',
    'when' => 'کەی',
    'key' => 'کلیل',
    'value' => 'بەها',
    'severity_label' => 'ئاستی گرنگی',

    'created_from' => 'دروستکراوە لە :date',
    'created_until' => 'دروستکراوە تا :date',

    'navigation' => [
        'group' => 'ڕێکخستنەکان',
        'label' => [
            'singular' => 'Tracer',
            'plural' => 'Tracers',
        ],
    ],

    'sections' => [
        'error' => 'ئیرۆر',
        'location' => 'شوێن',
        'request' => 'داواکاری',
    ],

    'severity' => [
        'critical' => 'زۆر گرنگ',
        'error' => 'ئیرۆر',
        'warning' => 'ئاگاداری',
        'notice' => 'تێبینی',
    ],

    'tabs' => [
        'details' => 'وردەکارییەکان',
        'exceptions' => 'پەنجەی هەڵە',
        'queries' => 'Queries',
        'body' => 'ناوەڕۆک',
        'cookies' => 'Cookies',
        'headers' => 'ناونیشانەکان',
    ],

    'queries' => [
        'connection' => 'پەیوەندی',
        'time' => 'کات',
        'bindings' => 'Bindings',
    ],

    'placeholders' => [
        'no_traces' => 'هیچ پەنجەی هەڵە تۆمار نەکراوە.',
        'no_queries' => 'هیچ پرسیارێکی داتابەیس تۆمار نەکراوە.',
        'no_body' => 'داواکارییەکە هیچ ناوەڕۆکێکی نەبوو.',
        'no_headers' => 'هیچ ناونیشانێک تۆمار نەکراوە.',
        'no_cookies' => 'هیچ کوکی نییە لە داواکاریەکەدا.',
    ],

    'tooltips' => [
        'copy_trace' => 'Trace لەبەرگیرا',
        'copy_path' => 'ڕێڕەو لەبەرگیرا',
        'copy_caller' => 'پەیوەندیکەر لەبەرگیرا',
        'copy_sql' => 'SQL Query لەبەرگیرا',
        'copy_key' => 'کلیل لەبەرگیرا',
        'copy_value' => 'بەها لەبەرگیرا',
        'copy_header' => 'ناونیشان لەبەرگیرا',
    ],

    'actions' => [
        'copy_report' => 'لەبەرگرتن وەک Markdown',
        'copied_title' => 'ڕاپۆرت لەبەرگیرا',
        'copied_body' => 'پوختەی Markdown ی ئەم tracer ەکە لە کلیپبۆردت دانرا.',
        'view_tooltip' => 'بینینی وردەکاری',
    ],

    'empty_state' => [
        'heading' => 'هەموو شت باشە',
        'description' => 'هیچ هەڵەیەک تۆمار نەکراوە. زۆر باشە.',
    ],

    'widget' => [
        'total' => 'کۆی tracerەکان',
        'total_description' => 'هەموو ئیرۆرە تۆمارکراوەکان',
        'today' => 'ئەمڕۆ',
        'today_description' => 'ئیرۆرەکانی ئەمڕۆ',
        'week' => 'حەوت ڕۆژی ڕابردوو',
        'week_description' => 'ڕەوتی حەوت ڕۆژی ڕابردوو',
        'top_error' => 'ئیرۆرە سەرەکییەکە',
        'occurrences' => 'جار ڕوویداوە',
        'no_errors' => 'هیچ ئیرۆرێک تۆمار نەکراوە',
    ],

    'empty' => 'بەتاڵ!',
];
