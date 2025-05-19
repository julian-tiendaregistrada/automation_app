<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExportTask extends Model
{
    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'category',
        'subcategory',
        'status',
        'file_path',
        'error_message',
        'progress',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function markAsProcessing(): void
    {
        $this->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);
    }

    public function markAsCompleted($filePath): void
    {
        $this->update([
            'status' => 'completed',
            'file_path' => $filePath,
            'completed_at' => now(),
            'progress' => 100,
        ]);
    }

    public function markAsFailed($error): void
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $error,
            'completed_at' => now(),
        ]);
    }

    public function updateProgress($progress): void
    {
        $this->update(['progress' => $progress]);
    }
}
