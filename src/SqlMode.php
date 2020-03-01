<?php

namespace Mpyw\LaravelMySqlSystemVariableManager;

use Mpyw\LaravelMySqlSystemVariableManager\Replacers\SqlModeReplacer;

class SqlMode
{
    /**
     * @param  mixed                                                             $modes
     * @return \Mpyw\LaravelMySqlSystemVariableManager\Replacers\SqlModeReplacer
     */
    public static function set(...$modes): SqlModeReplacer
    {
        return (new SqlModeReplacer())->set(...$modes);
    }

    /**
     * @param  mixed                                                             $modes
     * @return \Mpyw\LaravelMySqlSystemVariableManager\Replacers\SqlModeReplacer
     */
    public static function enable(...$modes): SqlModeReplacer
    {
        return (new SqlModeReplacer())->enable(...$modes);
    }

    /**
     * @param  mixed                                                             $modes
     * @return \Mpyw\LaravelMySqlSystemVariableManager\Replacers\SqlModeReplacer
     */
    public static function disable(...$modes): SqlModeReplacer
    {
        return (new SqlModeReplacer())->disable(...$modes);
    }
}
