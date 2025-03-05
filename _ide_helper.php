<?php

declare(strict_types=1);

namespace Illuminate\Database
{
    use Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables;

    if (false) {
        interface ConnectionInterface
        {
            /**
             * Set MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed $value
             * @return $this
             * @see ManagesSystemVariables
             */
            public function setSystemVariable(string $key, $value, bool $memoizeForReconnect = true);

            /**
             * Set MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @return $this
             * @see ManagesSystemVariables
             */
            public function setSystemVariables(array $values, bool $memoizeForReconnect = true);

            /**
             * Run callback temporarily setting MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed $value
             * @param  mixed ...$args
             * @return mixed
             * @see ManagesSystemVariables
             */
            public function usingSystemVariable(string $key, $value, callable $callback, ...$args);

            /**
             * Run callback temporarily setting MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed ...$args
             * @return mixed
             * @see ManagesSystemVariables
             */
            public function usingSystemVariables(array $values, callable $callback, ...$args);
        }

        class Connection implements ConnectionInterface
        {
            /**
             * Set MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed $value
             * @return $this
             * @see ManagesSystemVariables
             */
            public function setSystemVariable(string $key, $value, bool $memoizeForReconnect = true) {}

            /**
             * Set MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @return $this
             * @see ManagesSystemVariables
             */
            public function setSystemVariables(array $values, bool $memoizeForReconnect = true) {}

            /**
             * Run callback temporarily setting MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed $value
             * @param  mixed ...$args
             * @return mixed
             * @see ManagesSystemVariables
             */
            public function usingSystemVariable(string $key, $value, callable $callback, ...$args) {}

            /**
             * Run callback temporarily setting MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed ...$args
             * @return mixed
             * @see ManagesSystemVariables
             */
            public function usingSystemVariables(array $values, callable $callback, ...$args) {}
        }
    }
}

namespace Illuminate\Support\Facades
{
    use Illuminate\Database\Connection;
    use Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables;

    if (false) {
        class DB extends Facade
        {
            /**
             * Set MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed      $value
             * @return Connection
             * @see ManagesSystemVariables
             */
            public static function setSystemVariable(string $key, $value, bool $memoizeForReconnect = true) {}

            /**
             * Set MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @return Connection
             * @see ManagesSystemVariables
             */
            public static function setSystemVariables(array $values, bool $memoizeForReconnect = true) {}

            /**
             * Run callback temporarily setting MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed $value
             * @param  mixed ...$args
             * @return mixed
             * @see ManagesSystemVariables
             */
            public static function usingSystemVariable(string $key, $value, callable $callback, ...$args) {}

            /**
             * Run callback temporarily setting MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed ...$args
             * @return mixed
             * @see ManagesSystemVariables
             */
            public static function usingSystemVariables(array $values, callable $callback, ...$args) {}
        }
    }
}
