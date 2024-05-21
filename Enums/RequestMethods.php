<?php
namespace UT_Php\Enums;

enum RequestMethods
{
    case Get;
    case Head;
    case Post;
    case Put;
    case Delete;
    case Connect;
    case Options;
    case Trace;
    case Patch;
}
