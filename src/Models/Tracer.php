<?php

namespace Entensy\FilamentTracer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tracer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

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
