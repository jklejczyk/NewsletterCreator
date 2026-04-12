<?php

namespace App\Domain\Newsletter\Events;

use App\Domain\Article\Models\Article;
use App\Domain\Newsletter\Models\Newsletter;
use Illuminate\Database\Eloquent\Collection;

readonly class NewsletterCreated
{
    /**
     * @param  Collection<int, Article>  $articles
     */
    public function __construct(public Newsletter $newsletter, public Collection $articles) {}
}
