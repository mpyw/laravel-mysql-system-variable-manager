# Laravel MySQL System Variable Manager<br>[![Build Status](https://github.com/mpyw/laravel-mysql-system-variable-manager/actions/workflows/ci.yml/badge.svg?branch=master)](https://github.com/mpyw/laravel-mysql-system-variable-manager/actions) [![Coverage Status](https://coveralls.io/repos/github/mpyw/laravel-mysql-system-variable-manager/badge.svg?branch=migrate-ci)](https://coveralls.io/github/mpyw/laravel-mysql-system-variable-manager?branch=migrate-ci) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpyw/laravel-mysql-system-variable-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mpyw/laravel-mysql-system-variable-manager/?branch=master)

A tiny extension of `MySqlConnection` that manages **session** system variables

## Requirements

- PHP: `^7.3 || ^8.0`
- Laravel: `^6.0 || ^7.0 || ^8.0 || ^9.0`

## Installing

```bash
composer require mpyw/laravel-mysql-system-variable-manager
```

## Basic Usage

The default implementation is provided by `MySqlConnectionServiceProvider`, however, **package discovery is not available**.
Be careful that you MUST register it in **`config/app.php`** by yourself.

```php
<?php

return [

    /* ... */

    'providers' => [
        /* ... */

        Mpyw\LaravelMySqlSystemVariableManager\MySqlConnectionServiceProvider::class,

        /* ... */
    ],

];
```

```php
<?php

use Illuminate\Support\Facades\DB;

// Assign an auto-recoverable system variable
// The variable is reassigned on accidental disconnections
DB::setSystemVariable('long_query_time', 10.0);

// Assign a system variable without auto-recovery
DB::setSystemVariable('long_query_time', 10.0, false);

// Assign multiple variables
DB::setSystemVariables(['long_query_time' => 10.0, 'tx_isolation' => 'read-committed']);

// Assign a variable on a different connection
DB::connection('other_mysql_connection')->setSystemVariable('long_query_time', 10.0);

// Run callback temporarily assigning a variable
DB::usingSystemVariable('long_query_time', 10.0, function () {
    /* ... */
});

// Run callback temporarily assigning multiple variables
DB::usingSystemVariables(['long_query_time' => 10.0, 'tx_isolation' => 'read-committed'], function () {
    /* ... */
});

// Run callback replacing current value
// NOTE: You MUST declare closure return types.
DB::usingSystemVariables(
    [
        'long_query_time' => function (float $currentValue): float {
             return $currentValue + 5.0;
        },
        'sql_mode' => function (string $currentValue): string {
             return str_replace('ONLY_FULL_GROUP_BY', '', $currentValue);
        },
    ],
    function () {
        /* ... */
    }
);
```

**WARNING:**  
Don't use `DB::disconnect()` directly or auto-recovery won't be fired.  
Use **`DB::connection()->disconnect()`** instead.

## Advanced Usage

You can extend `MySqlConnection` with `ManagesSystemVariables` trait by yourself.

```php
<?php

namespace App\Providers;

use App\Database\MySqlConnection;
use Illuminate\Database\Connection;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Connection::resolverFor('mysql', function (...$parameters) {
            return new MySqlConnection(...$parameters);
        });
    }
}
```

```php
<?php

namespace App\Database;

use Illuminate\Database\Connection as BaseMySqlConnection;
use Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables;

class MySqlConnection extends BaseMySqlConnection
{
    use ManagesSystemVariables;
    
    public function withoutForeignKeyChecks(callable $callback, ...$args)
    {
        return $this->usingSystemVariable('foreign_key_checks', false, $callback, ...$args);
    }
    
    public function allowingPartialGroupBy(callable $callback, ...$args)
    {
        return $this->usingSystemVariable('sql_mode', function (string $mode): string {
            return str_replace('ONLY_FULL_GROUP_BY', '', $mode);
        }, $callback, ...$args);
    }
}
```

```php
<?php

use App\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

$post = new Post();
$post->user()->associate(Auth::user());
$post->save();

DB::withoutForeignKeyChecks(function () use ($post) {
    $post->user()->associate(null);
    $post->save();
});
```
