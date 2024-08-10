<?php

namespace App\Providers;

use App\Filament\Pages\ProjectStructure;
use App\Http\Livewire\WeatherWidget;
use Filament\Facades\Filament;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

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
        Filament::registerPages([
            ProjectStructure::class,
        ]);
        Filament::serving(function () {
            if (Auth::check() && Auth::user()->email === 'prem.shah8120@gmail.com') {
                Filament::registerPages([
                    ProjectStructure::class,
                ]);
            }
        });
    }
}
