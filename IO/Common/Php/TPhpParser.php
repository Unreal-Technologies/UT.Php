<?php
namespace UT_Php_Core\IO\Common\Php;

trait TPhpParser
{
    /**
     * @return string
     */
    public function declaration(): string 
    {
        return implode('', (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> select(function ($x) {
                if (is_array($x)) {
                    return $x[1];
                }
                return $x;
            })
            -> toArray());
    }
    
    /**
     * @return string
     */
    public function name(): string 
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 313;
            })[1];
    }
}