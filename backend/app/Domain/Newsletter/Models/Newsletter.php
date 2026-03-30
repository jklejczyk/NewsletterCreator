<?php

namespace App\Domain\Newsletter\Models;

use App\Domain\Newsletter\Enums\NewsletterStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Newsletter extends Model
{
    /** @use HasFactory<\Database\Factories\NewsletterFactory> */
    use HasFactory;

    protected $table = 'newsletters';

    protected $fillable = ['subject', 'content', 'sent_at', 'recipient_count', 'status'];

    protected $casts = [
        'status' => NewsletterStatus::class,
    ];

    /** @return HasMany<NewsletterSend, $this> */
    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }
}
