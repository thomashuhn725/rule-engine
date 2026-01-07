<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\NestedValue;
use App\Models\ReferenceValue;
use App\Models\StaticValue;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'static_value' => StaticValue::class,
            'reference_value' => ReferenceValue::class,
            'nested_value' => NestedValue::class,
        ]);
    }
}
