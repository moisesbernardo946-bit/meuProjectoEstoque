<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\URL;

use App\Models\Client;
use App\Models\Company;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
         // Força HTTPS em produção
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        // Criar storage link automaticamente
        if (!is_link(public_path('storage'))) {
            @symlink(storage_path('app/public'), public_path('storage'));
        }

        // Mapeia as strings em entity_type para as classes corretas
        Relation::morphMap([
            'client' => Client::class,
            'cliente' => Client::class, // se no teu BD tens 'cliente' (pt), mapeia também
            'company' => Company::class,
            'empresa' => Company::class, // opcional: se houver 'empresa' no BD
        ]);
    }
}
