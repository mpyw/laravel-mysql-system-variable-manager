<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager\Tests;

use Closure;
use Illuminate\Support\Facades\DB;
use Mpyw\LaravelMySqlSystemVariableManager\MySqlConnectionServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PDO;
use ReflectionProperty;
use Illuminate\Foundation\Application;

class TestCase extends BaseTestCase
{
    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app): void
    {
        $host = gethostbyname('mysql') !== 'mysql' // Is "mysql" valid hostname?
            ? 'mysql' // Local
            : '127.0.0.1'; // CI

        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => $host,
            'username' => 'testing',
            'password' => 'testing',
            'database' => 'testing',
        ]);
        $app['config']->set('database.connections.mysql:emulated', [
            'driver' => 'mysql',
            'host' => $host,
            'username' => 'testing',
            'password' => 'testing',
            'database' => 'testing',
            'options' => [
                PDO::ATTR_EMULATE_PREPARES => true,
            ],
        ]);
    }

    /**
     * @param  Application $app
     * @return string[]
     */
    protected function getPackageProviders($app): array
    {
        return [
            MySqlConnectionServiceProvider::class,
        ];
    }

    protected function onNativeConnection(callable $callback): void
    {
        $callback(DB::connection('mysql'));
    }

    protected function onEmulatedConnection(callable $callback): void
    {
        $callback(DB::connection('mysql:emulated'));
    }

    /**
     * @return Closure|PDO
     */
    protected function getConnectionPropertyValue(?string $connection, string $property)
    {
        $db = DB::connection($connection);
        $rp = new ReflectionProperty($db, $property);
        $rp->setAccessible(true);
        $value = $rp->getValue($db);

        assert($value instanceof Closure || $value instanceof PDO);

        return $value;
    }

    protected function assertPdoResolved(?string $connection): void
    {
        $this->assertInstanceOf(PDO::class, $this->getConnectionPropertyValue($connection, 'pdo'));
    }

    protected function assertPdoNotResolved(?string $connection): void
    {
        $this->assertInstanceOf(Closure::class, $this->getConnectionPropertyValue($connection, 'pdo'));
    }
}
