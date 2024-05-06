<?php

namespace UT_Php\Collections;

class Dictionary
{
    /**
     * @var array
     */
    private array $buffer = [];

    /**
     * @param  mixed $key
     * @param  mixed $value
     * @param  bool  $setAsArray
     * @return bool
     */
    public function add(mixed $key, mixed $value, bool $setAsArray = false): bool
    {
        if (isset($this -> buffer[$key])) {
            return false;
        }
        $this -> buffer[$key] = $setAsArray ? [$value] : $value;
        return true;
    }

    /**
     * @param  mixed $key
     * @return mixed
     */
    public function get(mixed $key): mixed
    {
        if (isset($this -> buffer[$key])) {
            return $this -> buffer[$key];
        }
        return null;
    }

    /**
     * @param  mixed $key
     * @return bool
     */
    public function remove(mixed $key): bool
    {
        if (isset($this -> buffer[$key])) {
            unset($this -> buffer[$key]);
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this -> buffer;
    }

    /**
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this -> buffer);
    }

    /**
     * @return array
     */
    public function values(): array
    {
        return array_values($this -> buffer);
    }
}
