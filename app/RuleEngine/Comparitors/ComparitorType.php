<?php

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

    public static function fromSymbol(string $symbol): self
    {
        return match ($symbol) {
            '==' => self::Equals,
            '>' => self::Greater,
            '<' => self::Less,
            '&&' => self::All,
            '||' => self::Any,
            '~' => self::Regex,
            '!' => self::Not,
            '===' => self::Strict,
            default => throw new \InvalidArgumentException("Unknown comparitor symbol: {$symbol}"),
        };
    }
}
