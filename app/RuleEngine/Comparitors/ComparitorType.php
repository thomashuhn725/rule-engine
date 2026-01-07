<?php

declare(strict_types=1);

namespace App\RuleEngine\Comparitors;

enum ComparitorType: string
{
    case Equals = '==';
    case Greater = '>';
    case Less = '<';
    case All = '&&';
    case Any = '||';
    case Regex = '~';
    case Not = '!';
    case Strict = '===';
}
