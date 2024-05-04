<?php
namespace UT_Php\Collections;

class Dictionary
{
    /**
     * @var array
     */
    private array $buffer = [];
    
    /**
     * @param mixed $key
     * @param mixed $value
     * @param bool $setAsArray
     * @return bool
     */
    public function Add(mixed $key, mixed $value, bool $setAsArray = false): bool
    {
        if(isset($this -> buffer[$key]))
        {
            return false;
        }
        $this -> buffer[$key] = $setAsArray ? [$value] : $value;
        return true;
    }
    
    /**
     * @param mixed $key
     * @return mixed
     */
    public function Get(mixed $key): mixed
    {
        if(isset($this -> buffer[$key]))
        {
            return $this -> buffer[$key];
        }
        return null;
    }
    
    /**
     * @param mixed $key
     * @return bool
     */
    public function Remove(mixed $key): bool
    {
        if(isset($this -> buffer[$key]))
        {
            unset($this -> buffer[$key]);
            return true;
        }
        return false;
    }
    
    /**
     * @return array
     */
    public function ToArray(): array
    {
        return $this -> buffer;
    }
    
    /**
     * @return array
     */
    public function Keys(): array
    {
        return array_keys($this -> buffer);
    }
    
    /** 
     * @return array
     */
    public function Values(): array
    {
        return array_values($this -> buffer);
    }
}