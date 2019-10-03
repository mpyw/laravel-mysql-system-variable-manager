<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

/**
 * Class MySqlConnectionServiceProvider
 */
class MySqlConnectionServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Connection::resolverFor('mysql', function (...$parameters) {
            return new MySqlConnection(...$parameters);
        });
    }
}
