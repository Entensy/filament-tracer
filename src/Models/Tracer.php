<?php

namespace Entensy\FilamentTracer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tracer extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function getTable()
    {
        return config('filament-tracer.database.table_name');
    }

    public function getKeyName()
    {
        return config('filament-tracer.database.primary_key');
    }
}
