<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        Gate::define("store-room", fn($user) => $user->role === "admin");
        Gate::define("update-room", fn($user) => $user->role === "admin");
        Gate::define("delete-room", fn($user) => $user->role === "admin");

        Gate::define("update-user", fn($user, $userInfo) => $user->role == "admin");
        Gate::define("delete-user", fn($user, $userInfo) => $user->role == "admin");

        Gate::define("change-role", fn($user) => $user->role === "admin");

        Gate::define("delete-book", fn($user) => $user->role == "admin");

        Gate::define("get-notifications", fn($user) => $user->role == "admin");

        Gate::define("add-class", fn($user) => $user->role == "admin");






    }
}
