<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Tests;

use Closure;
use Illuminate\Support\Facades\DB;
use Mpyw\LaravelMySqlSystemVariableManager\MySqlConnectionServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PDO;

class TestCase extends BaseTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $host = \gethostbyname('mysql') !== 'mysql' // Is "mysql" valid hostname?
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
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
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
     * @param  string        $property
     * @return \Closure|\PDO
     */
    protected function getConnectionPropertyValue(string $connection, string $property)
    {
        $db = DB::connection($connection);
        $rp = new \ReflectionProperty($db, $property);
        $rp->setAccessible(true);
        return $rp->getValue($db);
    }

    protected function assertPdoResolved(string $connection): void
    {
        $this->assertInstanceOf(PDO::class, $this->getConnectionPropertyValue($connection, 'pdo'));
    }

    protected function assertPdoNotResolved(string $connection): void
    {
        $this->assertInstanceOf(Closure::class, $this->getConnectionPropertyValue($connection, 'pdo'));
    }

    /**
     * @param mixed $v80
     * @param mixed $v81
     * @return mixed
     */
    protected function v81($v80, $v81)
    {
        // Since PHP 8.1, always get native number regardless of emulation.
        return version_compare(PHP_VERSION, '8.1', '<')
            ? $v80
            : $v81;
    }
}
