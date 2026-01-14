<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Schema::defaultStringLength(191);

        Blade::if('role', function (...$roles) {
            $user = auth()->user();

            if (in_array("in", $roles)) {
                if ($user->Groupp == "SECONDARYSEWINGINOUT" || $user->Groupp == "SECONDARYSEWINGIN") {
                    return true;
                }
            } else if (in_array("out", $roles)) {
                if ($user->Groupp == "SECONDARYSEWINGINOUT" || $user->Groupp == "SECONDARYSEWINGOUT") {
                    return true;
                }
            } else if (in_array("in_out", $roles)) {
                if ($user->Groupp == "SECONDARYSEWINGINOUT") {
                    return true;
                }
            }

            return false;
        });
    }
}
