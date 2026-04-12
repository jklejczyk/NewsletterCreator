<?php

namespace App\Domain\Newsletter\Queries;

use App\Domain\Newsletter\Models\Newsletter;
use Illuminate\Pagination\LengthAwarePaginator;

class GetNewslettersQueryHandler
{
    /** @return LengthAwarePaginator<int, Newsletter> */
    public function handle(GetNewslettersQuery $query): LengthAwarePaginator
    {
        return Newsletter::query()
            ->when($query->status, fn ($q, $status) => $q->where('status', $status))
            ->when($query->dateFrom, fn ($q, $date) => $q->where('sent_at', '>=', $date))
            ->when($query->dateTo, fn ($q, $date) => $q->where('sent_at', '<=', $date))
            ->latest()
            ->paginate($query->perPage);
    }
}
