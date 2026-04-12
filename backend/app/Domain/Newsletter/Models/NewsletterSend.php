<?php

namespace App\Domain\Newsletter\Models;

use Database\Factories\NewsletterSendFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsletterSend extends Model
{
    /** @use HasFactory<NewsletterSendFactory> */
    use HasFactory;

    protected $table = 'newsletter_sends';

    protected $fillable = ['newsletter_id', 'subscriber_id', 'sent_at'];

    /** @return BelongsTo<Newsletter, $this> */
    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    /** @return BelongsTo<Subscriber, $this> */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }
}
