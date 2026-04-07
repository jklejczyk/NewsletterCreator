<?php

namespace App\Providers;

use App\Domain\Article\Clients\NewsApiClient;
use App\Domain\Article\Clients\RssFeedIoClient;
use FeedIo\Adapter\Http\Client;
use FeedIo\FeedIo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpClient\HttplugClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->when(NewsApiClient::class)->needs('$apiKey')->giveConfig('services.newsapi.key');

        $this->app->bind(RssFeedIoClient::class, function ($app) {
            return new RssFeedIoClient(
                new FeedIo(new Client(new HttplugClient)),
                config('services.rss.feeds'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Factory::guessFactoryNamesUsing(
            fn (string $modelName): string => 'Database\\Factories\\'.class_basename($modelName).'Factory',
        );
    }
}
