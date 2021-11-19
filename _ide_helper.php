<?php

namespace Illuminate\Database
{
    if (false) {
        interface ConnectionInterface
        {
            /**
             * Set MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed $value
             * @return $this
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public function setSystemVariable(string $key, $value, bool $memoizeForReconnect = true);

            /**
             * Set MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  array  $values
             * @param  bool   $memoizeForReconnect
             * @return $this
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public function setSystemVariables(array $values, bool $memoizeForReconnect = true);

            /**
             * Run callback temporarily setting MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed $value
             * @param  mixed ...$args
             * @return mixed
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public function usingSystemVariable(string $key, $value, callable $callback, ...$args);

            /**
             * Run callback temporarily setting MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  array $values
             * @param  mixed ...$args
             * @return mixed
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
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
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public function setSystemVariable(string $key, $value, bool $memoizeForReconnect = true)
            {
            }

            /**
             * Set MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  array  $values
             * @param  bool   $memoizeForReconnect
             * @return $this
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public function setSystemVariables(array $values, bool $memoizeForReconnect = true)
            {
            }

            /**
             * Run callback temporarily setting MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed $value
             * @param  mixed ...$args
             * @return mixed
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public function usingSystemVariable(string $key, $value, callable $callback, ...$args)
            {
            }

            /**
             * Run callback temporarily setting MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  array $values
             * @param  mixed ...$args
             * @return mixed
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public function usingSystemVariables(array $values, callable $callback, ...$args)
            {
            }
        }
    }
}

namespace Illuminate\Support\Facades
{
    if (false) {
        class DB extends Facade
        {
            /**
             * Set MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed $value
             * @return \Illuminate\Database\Connection
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public static function setSystemVariable(string $key, $value, bool $memoizeForReconnect = true)
            {
            }

            /**
             * Set MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  array  $values
             * @param  bool   $memoizeForReconnect
             * @return \Illuminate\Database\Connection
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public static function setSystemVariables(array $values, bool $memoizeForReconnect = true)
            {
            }

            /**
             * Run callback temporarily setting MySQL system variable for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  mixed $value
             * @param  mixed ...$args
             * @return mixed
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public static function usingSystemVariable(string $key, $value, callable $callback, ...$args)
            {
            }

            /**
             * Run callback temporarily setting MySQL system variables for both read and write PDOs.
             * It is lazily executed for unresolved PDO instance.
             *
             * @param  array $values
             * @param  mixed ...$args
             * @return mixed
             * @see \Mpyw\LaravelMySqlSystemVariableManager\ManagesSystemVariables
             */
            public static function usingSystemVariables(array $values, callable $callback, ...$args)
            {
            }
        }
    }
}
