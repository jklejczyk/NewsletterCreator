<?php

namespace App\Domain\Newsletter\Models;

use App\Domain\Article\Enums\ArticleCategory;
use Database\Factories\SubscriberFactory;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * @property Collection<int, ArticleCategory>|null $preferences
 * @property Carbon|null $confirmation_sent_at
 */
class Subscriber extends Model
{
    /** @use HasFactory<SubscriberFactory> */
    use HasFactory;

    protected $table = 'subscribers';

    protected $fillable = [
        'email',
        'name',
        'preferences',
        'is_active',
        'confirmed_at',
        'confirmation_token',
        'confirmation_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'preferences' => AsEnumCollection::of(ArticleCategory::class),
            'is_active' => 'boolean',
            'confirmed_at' => 'datetime',
            'confirmation_sent_at' => 'datetime',
        ];
    }

    /** @return HasMany<NewsletterSend, $this> */
    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }

    /** @return HasManyThrough<Newsletter, NewsletterSend, $this> */
    public function newsletters(): HasManyThrough
    {
        return $this->hasManyThrough(Newsletter::class, NewsletterSend::class);
    }
}
