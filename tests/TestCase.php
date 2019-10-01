<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Tests;

use Illuminate\Support\Facades\DB;
use Mpyw\LaravelMySqlSystemVariableManager\LaravelMySqlSystemVariableManagerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PDO;

class TestCase extends BaseTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
        $app['config']->set('database.connections.mysql', [
            'driver' => 'mysql',
            'host' => 'mysql',
            'username' => 'user',
            'password' => 'password',
            'database' => 'testing',
        ]);
        $app['config']->set('database.connections.mysql:emulated', [
            'driver' => 'mysql',
            'host' => 'mysql',
            'username' => 'user',
            'password' => 'password',
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
            LaravelMySqlSystemVariableManagerServiceProvider::class,
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
}
