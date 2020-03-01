<?php

namespace Mpyw\LaravelMySqlSystemVariableManager\Replacers;

use Illuminate\Support\Arr;
use Mpyw\LaravelMySqlSystemVariableManager\ExpressionInterface;
use Mpyw\LaravelMySqlSystemVariableManager\ExpressionTrait;

class SqlModeReplacer implements StringReplacerInterface
{
    use ExpressionTrait;

    /**
     * @var null|string[]
     */
    protected $set;

    /**
     * @var string[]
     */
    protected $enabled = [];

    /**
     * @var string[]
     */
    protected $disabled = [];

    /**
     * @param  string[]|string[][] $modes
     * @return $this
     */
    public function set(...$modes)
    {
        $modes = static::normalizeModes($modes);

        $this->set = $modes;
        $this->enabled = [];
        $this->disabled = [];

        return $this;
    }

    /**
     * @param  string[]|string[][] $modes
     * @return $this
     */
    public function enable(...$modes)
    {
        $modes = static::normalizeModes($modes);

        $this->enabled = array_values(array_unique(array_merge($this->enabled, $modes)));
        $this->disabled = array_values(array_diff($this->disabled, $modes));

        return $this;
    }

    /**
     * @param  string[]|string[][] $modes
     * @return $this
     */
    public function disable(...$modes)
    {
        $modes = static::normalizeModes($modes);

        $this->enabled = array_values(array_diff($this->enabled, $modes));
        $this->disabled = array_values(array_unique(array_merge($this->disabled, $modes)));

        return $this;
    }

    /**
     * @param  array    $modes
     * @return string[]
     */
    protected static function normalizeModes(array $modes): array
    {
        return array_values(array_filter(explode(',', implode(',', Arr::flatten($modes))), 'strlen'));
    }

    /**
     * Return type.
     *
     * @return string
     */
    public function getType(): string
    {
        return ExpressionInterface::TYPE_STRING;
    }

    /**
     * Replace string variable value.
     *
     * @param  string $value
     * @return string
     */
    public function replace(string $value): string
    {
        $value = static::normalizeModes($this->set ?? (array)$value);

        $value = array_diff($value, $this->disabled);
        $value = array_merge($value, $this->enabled);

        return implode(',', $value);
    }
}
