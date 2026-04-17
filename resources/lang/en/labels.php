<?php

return [
    'id' => 'ID',
    'source' => 'Source',
    'error_type' => 'Error Type',
    'path' => 'Path',
    'ip' => 'IP Address',
    'code' => 'Code',
    'message' => 'Message',
    'line' => 'Line',
    'method' => 'Method',
    'file' => 'File',
    'trace' => 'Trace',
    'traces' => 'Traces',
    'caller' => 'Caller',
    'queries' => 'Queries',
    'body' => 'Body',
    'headers' => 'Headers',
    'cookies' => 'Cookies',
    'created_at' => 'Created At',
    'when' => 'When',
    'key' => 'Key',
    'value' => 'Value',
    'severity_label' => 'Severity',

    'created_from' => 'Created From :date',
    'created_until' => 'Created Until :date',

    'navigation' => [
        'group' => 'Diagnostics',
        'label' => [
            'singular' => 'Tracer',
            'plural' => 'Tracers',
        ],
    ],

    'sections' => [
        'error' => 'Error',
        'location' => 'Location',
        'request' => 'Request',
    ],

    'severity' => [
        'critical' => 'Critical',
        'error' => 'Error',
        'warning' => 'Warning',
        'notice' => 'Notice',
    ],

    'tabs' => [
        'details' => 'Details',
        'exceptions' => 'Stack trace',
        'queries' => 'Queries',
        'body' => 'Body',
        'cookies' => 'Cookies',
        'headers' => 'Headers',
    ],

    'queries' => [
        'connection' => 'Connection',
        'time' => 'Duration',
        'bindings' => 'Bindings',
    ],

    'placeholders' => [
        'no_traces' => 'No stack trace was captured for this error.',
        'no_queries' => 'No database queries were recorded during this request.',
        'no_body' => 'The request had no body payload.',
        'no_headers' => 'No request headers were captured.',
        'no_cookies' => 'No cookies were present on the request.',
    ],

    'tooltips' => [
        'copy_trace' => 'Trace Copied',
        'copy_path' => 'Path Copied',
        'copy_caller' => 'Caller Copied',
        'copy_sql' => 'SQL Query Copied',
        'copy_key' => 'Key Copied',
        'copy_value' => 'Value Copied',
        'copy_header' => 'Header Copied',
    ],

    'actions' => [
        'copy_report' => 'Copy as Markdown',
        'copied_title' => 'Report copied',
        'copied_body' => 'A Markdown summary of this tracer was placed on your clipboard.',
        'view_tooltip' => 'View details',
    ],

    'empty_state' => [
        'heading' => 'All clear',
        'description' => 'No errors have been captured yet. Nice.',
    ],

    'widget' => [
        'total' => 'Total tracers',
        'total_description' => 'All captured errors',
        'today' => 'Today',
        'today_description' => 'Errors captured today',
        'week' => 'Last 7 days',
        'week_description' => 'Trend over the past week',
        'top_error' => 'Top error',
        'occurrences' => 'occurrences',
        'no_errors' => 'No errors captured',
    ],

    'empty' => 'Empty!',
];
