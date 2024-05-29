<?php

namespace UT_Php_Core\IO\Common\Php;

enum ReplaceTypes
{
    case Member;
    case Variable;
    case Method;
    case Constant;
    case Declaration;
}
