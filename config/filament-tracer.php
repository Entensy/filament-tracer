<?php

return [
    // You may implement your own tracer by implementing Tracerable interface
    'tracer' => \Entensy\FilamentTracer\DefaultTracer::class,
    'model' => \Entensy\FilamentTracer\Models\Tracer::class,

    'source' => 'filament-php',

    'database' => [
        'table_name' => 'tracers',
        'primary_key' => 'tracer_id',
    ],

    'filament' => [
        'resource' => \Entensy\FilamentTracer\Resources\TracerResource::class,
        'list' => \Entensy\FilamentTracer\Resources\TracerResource\Pages\ListTracers::class,
        'view' => \Entensy\FilamentTracer\Resources\TracerResource\Pages\ViewTracer::class,

        'slug' => 'tracers',

        'navigation' => [
            'enabled' => true,
            'enable_badge' => true,
            'icon' => 'heroicon-o-exclamation-circle',
            'sort' => 1,
        ],

        'table' => [
            'id_sortable' => true,
            'id_toggleable' => true,

            'source_toggleable' => true,
            'source_sortable' => true,
            'source_searchable' => true,

            'error_type_toggleable' => true,
            'error_type_sortable' => true,
            'error_type_searchable' => true,

            'code_toggleable' => true,
            'code_sortable' => true,
            'code_searchable' => false,

            'file_toggleable' => true,
            'file_sortable' => false,
            'file_searchable' => true,

            'line_toggleable' => true,
            'line_sortable' => true,

            'method_toggleable' => true,
            'method_sortable' => true,
            'method_searchable' => true,

            'ip_toggleable' => true,
            'ip_sortable' => false,
            'ip_searchable' => false,

            'path_toggleable' => true,
            'path_sortable' => false,
            'path_searchable' => true,
            'path_text_limit' => 64,

            'created_at_toggleable' => true,
            'created_at_sortable' => true,
            'created_at_format' => 'd/m/Y H:i:s',

            'default_sort' => 'tracer_id',
            'sort_direction' => 'desc',

            'enable_bulk_delete' => true,
        ],

        'cookies' => [
            'hide_null_values' => true,
        ],
    ],

    // Skip capturing follwing Exceptions
    'except' => [
        // \Symfony\Component\Routing\Exception\RouteNotFoundException::class,
    ],
];
