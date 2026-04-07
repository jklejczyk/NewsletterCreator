<?php

namespace App\Domain\Newsletter\Models;

use Database\Factories\SubscriberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Subscriber extends Model
{
    /** @use HasFactory<SubscriberFactory> */
    use HasFactory;

    protected $table = 'subscribers';

    protected $fillable = ['email', 'name', 'preferences', 'is_active', 'confirmed_at'];

    protected $casts = [
        'preferences' => 'array',
        'is_active' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

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
