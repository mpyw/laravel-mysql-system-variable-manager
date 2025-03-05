<?php

declare(strict_types=1);

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Illuminate\Database\MySqlConnection as BaseMySqlConnection;

/**
 * Class MySqlConnection
 */
class MySqlConnection extends BaseMySqlConnection
{
    use ManagesSystemVariables;
}
