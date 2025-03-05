<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

/**
 * Class MySqlConnectionServiceProvider
 */
class MySqlConnectionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Connection::resolverFor('mysql', static function (...$parameters) {
            return new MySqlConnection(...$parameters);
        });
    }
}
