<?php

namespace App\Http\Resources;

use App\Domain\Newsletter\Models\NewsletterSend;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin NewsletterSend */
class NewsletterSendResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'subscriber' => new SubscriberResource($this->whenLoaded('subscriber')),
            'sent_at' => $this->sent_at,
        ];
    }
}
