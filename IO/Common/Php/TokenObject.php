<?php
namespace UT_Php_Core\IO\Common\Php;

class TokenObject implements IObject
{
    private array $tokens;
    
    public function __construct(array $tokens) 
    {
        $this -> tokens = $tokens;
    }
    
    public function declaration(): string 
    {
        throw new \UT_Php_Core\Exceptions\NotImplementedException();
    }
    
    public function name(): string 
    {
        throw new \UT_Php_Core\Exceptions\NotImplementedException();
    }
}
