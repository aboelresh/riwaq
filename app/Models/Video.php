<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'filename',
        'path',
        'size',
        'duration',
        'uploaded_by',
        'topic_id',
    ];

    protected $casts = [
        'size' => 'integer',
        'duration' => 'integer',
    ];

    protected $appends = ['size_in_m_b'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }

    public function getSizeInMBAttribute(): float
    {
        return round($this->size / 1024 / 1024, 2);
    }
}