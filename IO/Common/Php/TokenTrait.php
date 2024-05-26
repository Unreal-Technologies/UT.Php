<?php
namespace UT_Php_Core\IO\Common\Php;

class TokenTrait implements ITrait
{
    use TPhpParser;
    /**
     * @var array
     */
    private array $tokens;
    
    /**
     * @param array $tokens
     */
    public function __construct(array $tokens) 
    {
        $this -> tokens = $tokens;
    }
}