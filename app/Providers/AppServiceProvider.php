<?php

namespace App\Providers;

use App\AI\AIProviderInterface;
use App\AI\Providers\MockAIProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AIProviderInterface::class, function () {

            return new MockAIProvider();

            // return new OpenAIProvider();
        });
    }
    /**
     * Bootstrap any application services.
     */

    public function boot(): void
    {
            Relation::morphMap([
                'course' => \App\Models\Course::class,
            ]);
    }}
