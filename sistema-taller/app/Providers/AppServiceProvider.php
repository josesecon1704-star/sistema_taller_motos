<?php

namespace App\Providers;

use App\Models\Bitacora;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
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
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
        Gate::define(
            'permiso',
            fn(User $user, string $modulo, string $accion) =>
            $user->tienePermiso($modulo, $accion)
        );
        Event::listen(Login::class, function (Login $e) {
            Bitacora::create([
                'user_id'        => $e->user->getAuthIdentifier(),
                'accion'         => 'login',
                'tabla_afectada' => 'users',
                'registro_id'    => $e->user->getAuthIdentifier(),
            ]);
        });

        Event::listen(Logout::class, function (Logout $e) {
            if ($e->user) {
                Bitacora::create([
                    'user_id'        => $e->user->getAuthIdentifier(),
                    'accion'         => 'logout',
                    'tabla_afectada' => 'users',
                    'registro_id'    => $e->user->getAuthIdentifier(),
                ]);
            }
        });

        Event::listen(Failed::class, function (Failed $e) {
            Bitacora::create([
                'user_id'        => null,
                'accion'         => 'login_fallido',
                'tabla_afectada' => 'users',
                'registro_id'    => $e->user?->getAuthIdentifier(),
                'valor_nuevo'    => json_encode(['email' => $e->credentials['email'] ?? null], JSON_UNESCAPED_UNICODE),
            ]);
        });
    }
}
