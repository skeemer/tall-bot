<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
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
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => Blade::render("@vite(['resources/js/chat.js'])"),
        );
        FilamentView::registerRenderHook(
            PanelsRenderHook::SIDEBAR_START,
            fn (): string => Blade::render('<livewire:connect-event-sub/>'),
        );
    }
}
