<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Illuminate\Database\MySqlConnection as BaseMySqlConnection;

/**
 * Class MySqlConnection
 */
class MySqlConnection extends BaseMySqlConnection
{
    use ManagesSystemVariables;
}
