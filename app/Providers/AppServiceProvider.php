<?php

namespace App\Providers;

use App\Models\User;
use Livewire\Livewire;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use App\Livewire\Invitation\PublicView;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Gate::policy(User::class, UserPolicy::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
