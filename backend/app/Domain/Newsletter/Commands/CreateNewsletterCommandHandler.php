<?php

namespace App\Domain\Newsletter\Commands;

use App\Domain\Article\Models\Article;
use App\Domain\Newsletter\Enums\NewsletterStatus;
use App\Domain\Newsletter\Events\NewsletterCreated;
use App\Domain\Newsletter\Models\Newsletter;

class CreateNewsletterCommandHandler
{
    public function handle(CreateNewsletterCommand $command): void
    {
        $articles = Article::where('is_processed', true)
            ->where('imported_at', '>=', now()->subDay())
            ->get();

        if (!$articles->count()) return;

        $content = '';
        foreach ($articles as $article) {
            $content .= $article->summary . PHP_EOL;
        }

        $newsletter = Newsletter::create([
            'subject' => 'Twój codzienny newsletter — ' . now()->format('d.m.Y'),
            'content' => $content,
            'status' => NewsletterStatus::DRAFT,
            'recipient_count' => 0,
        ]);
        event(new NewsletterCreated($newsletter, $articles));
    }
}
