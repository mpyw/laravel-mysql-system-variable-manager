<?php

namespace Mpyw\LaravelMysqlSystemVariableManager;

use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

/**
 * Class LaravelMysqlSystemVariableManagerServiceProvider
 */
class LaravelMysqlSystemVariableManagerServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Connection::resolverFor('mysql', function (...$parameters) {
            return new MySqlConnection(...$parameters);
        });
    }
}
