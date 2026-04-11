<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Newsletter\Commands\SubscribeCommand;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SubscribeRequest;
use App\Infrastructure\Bus\CommandBus;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class SubscriberController extends Controller
{
    public function store(SubscribeRequest $request, CommandBus $bus): JsonResponse
    {
        $bus->dispatch(new SubscribeCommand(
            email: $request->validated('email'),
            name: $request->validated('name'),
            preferences: $request->categories(),
        ));

        return response()->json(
            ['message' => 'Rejestracja przyjęta. Sprawdź skrzynkę e-mail.'],
            201,
        );
    }

    public function confirm(string $token, CommandBus $bus): JsonResponse
    {
        //TODO
    }

    public function destroy(int $id, CommandBus $bus): JsonResponse
    {
        //TODO
    }
}
