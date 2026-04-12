<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Newsletter\Commands\CreateNewsletterCommand;
use App\Domain\Newsletter\Enums\NewsletterStatus;
use App\Domain\Newsletter\Models\Newsletter;
use App\Domain\Newsletter\Queries\GetNewslettersQuery;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsletterResource;
use App\Infrastructure\Bus\CommandBus;
use App\Infrastructure\Bus\QueryBus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class NewsletterController extends Controller
{
    public function index(Request $request, QueryBus $bus): AnonymousResourceCollection
    {
        $result = $bus->ask(new GetNewslettersQuery(
            status: $request->enum('status', NewsletterStatus::class),
            dateFrom: $request->input('date_from'),
            dateTo: $request->input('date_to'),
            perPage: $request->integer('per_page', 20),
        ));

        return NewsletterResource::collection($result);
    }

    public function stats(Newsletter $newsletter): NewsletterResource
    {
        $newsletter->load('sends.subscriber');

        return new NewsletterResource($newsletter);
    }

    public function send(CommandBus $bus): JsonResponse
    {
        $bus->dispatch(new CreateNewsletterCommand());

        return response()->json(
            ['message' => 'Newsletter dispatch initiated.'],
            202,
        );
    }
}
