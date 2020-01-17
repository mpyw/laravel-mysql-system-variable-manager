# Laravel MySQL System Variable Manager<br>[![Build Status](https://travis-ci.com/mpyw/laravel-mysql-system-variable-manager.svg?branch=master)](https://travis-ci.com/mpyw/laravel-mysql-system-variable-manager) [![Code Coverage](https://scrutinizer-ci.com/g/mpyw/laravel-mysql-system-variable-manager/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/mpyw/laravel-mysql-system-variable-manager/?branch=master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mpyw/laravel-mysql-system-variable-manager/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mpyw/laravel-mysql-system-variable-manager/?branch=master)

A tiny extension of `MySqlConnection` that manages **session** system variables

## Requirements

- PHP: ^7.1
- Laravel: ^5.8 || ^6.0

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
        $this->setSystemVariable('foreign_key_checks', false);
        try {
            return $callback(...$args);
        } finally {
            $this->setSystemVariable('foreign_key_checks', true);
        }
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
