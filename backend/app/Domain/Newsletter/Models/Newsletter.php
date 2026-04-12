<?php

namespace App\Domain\Newsletter\Models;

use App\Domain\Newsletter\Enums\NewsletterStatus;
use Database\Factories\NewsletterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsletter extends Model
{
    /** @use HasFactory<NewsletterFactory> */
    use HasFactory;

    protected $table = 'newsletters';

    protected $fillable = ['subject', 'content', 'sent_at', 'recipient_count', 'status'];

    protected $casts = [
        'status' => NewsletterStatus::class,
        'sent_at' => 'datetime',
    ];

    /** @return HasMany<NewsletterSend, $this> */
    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }
}
