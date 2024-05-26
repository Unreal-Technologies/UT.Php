<?php
namespace UT_Php_Core\IO\Common\Php;

trait TAccess
{
    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 362;
            }) !== null;
    }
    
    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 360;
            }) !== null;
    }
    
    /**
     * @return bool
     */
    public function isProtected(): bool
    {
        return (new \UT_Php_Core\Collections\Linq($this -> tokens))
            -> firstOrDefault(function ($x) {
                return is_array($x) && $x[0] === 361;
            }) !== null;
    }
}