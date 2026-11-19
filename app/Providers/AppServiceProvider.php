<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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
        // @rp($angka) -> Rp25.000
        Blade::directive('rp', fn($expression) => "<?php echo 'Rp' . number_format({$expression}, 0, ',', '.'); ?>");
    }
}
