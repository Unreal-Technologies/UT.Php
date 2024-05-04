<?php
namespace UT_Php\Collections;

class Linq
{
    private const WHERE = 1;
    private const SELECT = 2;
    private const GROUPBY = 4;
    private const SUM = 8;
    private const AVG = 16;
    private const ORDERBY = 32;
    
    /**
     * @var array
     */
    private array $inCollection;
    
    /**
     * @var array
     */
    private array $outCollection = [];
    
    /**
     * @var array
     */
    private array $query = [];
    
    /**
     * @var bool
     */
    private bool $isGrouped = false;
    
    /**
     * @var int[]
     */
    private array $counts = [];

    /**
     * @param array $collection
     */
    public function __construct(array $collection)
    {
        $this -> inCollection = $collection;
    }
    
    /**
     * @param \Closure $lambda
     * @return Linq
     */
    public function Where(\Closure $lambda): Linq
    {
        $count = count($this -> query);
        $index = $count - 1;
        if($count > 0 && $this -> query[$index][0] == $this::WHERE)
        {
            $l1 = $this -> query[$index][1];
            $this -> query[$index] = [$this::WHERE, function($x) use ($l1, $lambda)
            {
                return $l1($x) && $lambda($x);
            }];
        }
        else  
        {
            $this -> query[] = [$this::WHERE, $lambda];
        }
        return $this;
    }
    
    /**
     * @param \Closure $lambda
     * @return Linq
     */
    public function Select(\Closure $lambda): Linq
    {
        $this -> query[] = [$this::SELECT, $lambda];
        return $this;
    }
    
    /**
     * @param \Closure $lambda
     * @return Linq
     */
    public function GroupBy(\Closure $lambda): Linq
    {
        $this -> query[] = [$this::GROUPBY, $lambda];
        $this -> isGrouped = true;
        return $this;
    }
    
    /**
     * @param \Closure $lambda
     * @return array
     */
    public function ToArray(\Closure $lambda = null): array
    {
        $self = $lambda === null ? $this : $this -> Where($lambda);
        if(count($self -> outCollection) === 0)
        {
            $self -> Execute();
        }
        return $self -> outCollection;
    }
    
    /**
     * @param \Closure $lambda
     * @return mixed
     */
    public function FirstOrDefault(\Closure $lambda = null): mixed
    {
        $self = $lambda === null ? $this : $this -> Where($lambda);
        if(count($self -> outCollection) === 0)
        {
            $self -> Execute();
        }
        if(count($self -> outCollection) === 0)
        {
            return null;
        }
        $key = array_keys($self -> outCollection)[0];
        return $self -> outCollection[$key];
    }
    
    /**
     * @return int
     */
    public function Count(): int
    {
        if(count($this -> outCollection) === 0)
        {
            $this -> Execute();
        }
        return count($this -> outCollection);
    }
    
    /**
     * @param \Closure $lambda
     * @return Linq
     */
    public function Sum(\Closure $lambda = null): Linq
    {
        $this -> query[] = [$this::SUM, $lambda];
        return $this;
    }
    
    /**
     * @param \Closure $lambda
     * @return Linq
     */
    public function Avg(\Closure $lambda = null): Linq
    {
        $self = $this -> Sum($lambda);
        $self -> query[] = [$this::AVG, null];
        return $self;
    }
    
    /**
     * @param \Closure $lambda
     * @param \UT_Php\Enums\SortDirections $direction
     * @return Linq
     */
    public function OrderBy(\Closure $lambda = null, \UT_Php\Enums\SortDirections $direction = \UT_Php\Enums\SortDirections::Asc): Linq
    {
        $this -> query[] = [$this::ORDERBY, $lambda, $direction];
        return $this;
    }
    
    /**
     * @param int $index
     * @param array $buffer
     * @param \Closure $lambda
     * @param mixed $item
     * @return void
     */
    private function Execute_Switch_Where(int $index, array &$buffer, \Closure $lambda, mixed $item): void
    {
        if($lambda($item))
        {
            $buffer[$index] = $item;
        }
    }
    
    /**
     * @param mixed $buffer
     * @param \Closure $lambda
     * @param mixed $item
     * @return void
     */
    private function Execute_Switch_GroupBy(mixed &$buffer, \Closure $lambda, mixed $item): void
    {
        $key = $lambda($item);
        if(is_array($buffer))
        {
            $buffer = new Dictionary();
        }

        if(!$buffer -> Add($key, $item, true))
        {
            $prevItem = $buffer -> Get($key)[0];
            $list = [$prevItem, $item];
            $buffer -> Remove($key);
            $buffer -> Add($key, $list);
        }
    }
    
    /**
     * @param int $index
     * @param mixed $buffer
     * @param \Closure $lambda
     * @param mixed $item
     * @param array $collection
     * @return void
     */
    private function Execute_Switch_Sum(int $index, mixed &$buffer, \Closure $lambda, mixed $item, array $collection): void
    {
        if(!isset($this -> counts[$index]))
        {
            $this -> counts[$index] = 0;
        }
        
        if($this -> isGrouped)
        {
            if(count($buffer) === 0)
            {
                $buffer = array_fill_keys(array_keys($collection), 0);
            }
            $this -> counts[$index] += count($item);
            foreach($item as $v)
            {
                $value = $lambda == null ? $v : $lambda($v);
                $buffer[$index] += $value;
            }
        }
        else
        {
            if(is_array($buffer))
            {
                $buffer = 0;
            }
            
            $buffer += $lambda == null ? $item : $lambda($item);
            $this -> counts[$index]++;
        }
    }
    
    /**
     * @param int $index
     * @param mixed $buffer
     * @param \Closure|null $lambda
     * @param mixed $item
     */
    private function Execute_Switch_OrderBy(int $index, mixed &$buffer, ?\Closure $lambda, mixed $item)
    {
        if($lambda === null)
        {
            $buffer[$index] = [ $item ];
        }
        else
        {
            $key = $lambda($item);
            if(!isset($buffer[$key]))
            {
                $buffer[$key] = [];
            }

            $buffer[$key][] = $item;
        }
    }
    
    /**
     * @param int $type
     * @param int $index
     * @param mixed $buffer
     * @param \Closure|null $lambda
     * @param mixed $item
     * @param array $collection
     * @return void
     * @throws \UT_Php\Exceptions\NotImplementedException
     */
    private function Execute_Switch(int $type, int $index, mixed &$buffer, ?\Closure $lambda, mixed $item, array $collection): void
    {
        switch($type)
        {
            case $this::WHERE:
                $this -> Execute_Switch_Where($index, $buffer, $lambda, $item);
                break;

            case $this::SELECT:
                $buffer[$index] = $lambda($item);
                break;

            case $this::GROUPBY:
                $this -> Execute_Switch_GroupBy($buffer, $lambda, $item);
                break;

            case $this::SUM:
                $this -> Execute_Switch_Sum($index, $buffer, $lambda, $item, $collection);
                break;
            
            case $this::AVG:
                $buffer[$index] = $item / $this -> counts[$index];
                unset($this -> counts[$index]);
                break;
            
            case $this::ORDERBY:
                $this -> Execute_Switch_OrderBy($index, $buffer, $lambda, $item);
                break;
            
            default:
                throw new \UT_Php\Exceptions\NotImplementedException($type);
        }
    }
    
    /**
     * @return void
     */
    private function Execute(): void
    {
        $collection = $this -> inCollection;
        foreach($this -> query as $query)
        {
            $type = $query[0];
            $lambda = $query[1];
            
            if($type === $this::SUM)
            {
                $this -> counts = [];
            }
            
            $buffer = [];
            foreach($collection as $i => $item)
            {
                $this -> Execute_Switch($type, $i, $buffer, $lambda, $item, $collection);
            }
            
            if($type === $this::ORDERBY)
            {
                $direction = $query[2];
                if($direction == \UT_Php\Enums\SortDirections::Asc)
                {
                    ksort($buffer);
                }
                else
                {
                    krsort($buffer);
                }
                $buffer = $this -> MultiToSingleArray($buffer);
            }
            $collection = is_array($buffer) ? $buffer : $buffer -> ToArray();
        }
        $this -> outCollection = $collection;
    }
    
    /**
     * @param array $data
     * @return array
     */
    private function MultiToSingleArray(array $data): array
    {
        $buffer = [];
        
        foreach($data as $items)
        {
            foreach($items as $item)
            {
                $buffer[] = $item;
            }
        }
        
        return $buffer;
    }
}