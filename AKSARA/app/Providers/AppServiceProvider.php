<?php

namespace App\Providers;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\LombaModel;
use App\Models\PrestasiModel;
use App\Models\KeahlianUserModel;
use App\Observers\NotifikasiLombaObserver;
use App\Observers\NotifikasiPrestasiObserver;
use App\Observers\NotifikasiKeahlianObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        // Mendaftarkan observer untuk setiap model yang relevan
        LombaModel::observe(NotifikasiLombaObserver::class);
        PrestasiModel::observe(NotifikasiPrestasiObserver::class);
        KeahlianUserModel::observe(NotifikasiKeahlianObserver::class);


        DB::listen(function (QueryExecuted $query) {
            Log::info("SQL: {$query->sql}", $query->bindings);
        });

        
    }
}
