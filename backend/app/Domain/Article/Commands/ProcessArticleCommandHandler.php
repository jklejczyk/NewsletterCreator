<?php

namespace App\Domain\Article\Commands;

use App\Interfaces\AiClientInterface;

class ProcessArticleCommandHandler
{
    public function __construct(private AiClientInterface $aiClient) {}

    public function handle(ProcessArticleCommand $command): void
    {
        $summary = $this->aiClient->summarize($command->article->content);
        $category = $this->aiClient->categorize($command->article->content);

        $command->article->update(['summary' => $summary, 'category' => $category, 'is_processed' => true]);
    }
}
