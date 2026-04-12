<?php

namespace App\Http\Resources;

use App\Domain\Newsletter\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Newsletter */
class NewsletterResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'status' => $this->status,
            'sent_at' => $this->sent_at?->format('Y-m-d H:i'),
            'recipient_count' => $this->recipient_count,
            'sends' => NewsletterSendResource::collection($this->whenLoaded('sends')),
        ];
    }
}
