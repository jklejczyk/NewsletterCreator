<?php

namespace App\Infrastructure\Bus;

use Illuminate\Container\Container;

class CommandBus
{
    public function __construct(private Container $container) {}

    public function dispatch(object $command): void
    {
        $handlerClass = get_class($command).'Handler';

        $handler = $this->container->make($handlerClass);
        $handler->handle($command);
    }
}
