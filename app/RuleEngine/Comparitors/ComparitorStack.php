<?php

namespace App\RuleEngine\Comparitors;

use App\RuleEngine\Comparitors\Handlers\Both;
use App\RuleEngine\Comparitors\Handlers\Either;
use App\RuleEngine\Comparitors\Handlers\Equals;
use App\RuleEngine\Comparitors\Handlers\Greater;
use App\RuleEngine\Comparitors\Handlers\Less;
use App\RuleEngine\Comparitors\Handlers\Not;
use App\RuleEngine\Comparitors\Handlers\Regex;
use App\RuleEngine\Comparitors\Handlers\Strict;

class ComparitorStack
{
    private const string HANDLER_DIRECTORY = __DIR__.'/Handlers';

    private ?ComparitorHandler $handlerStack = null;

    private static ?self $instance = null;

    /**
     * @param  ComparitorHandler[]  $comparitors
     */
    private function __construct(array $comparitors)
    {
        $this->buildStack($comparitors);
    }

    public static function make(): self
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $handlers = self::getHandlersFromDirectory(self::HANDLER_DIRECTORY);
        self::$instance = new self($handlers);

        return self::$instance;
    }

    public static function get(): ?ComparitorHandler
    {
        return self::make()->handlerStack;
    }

    /**
     * @return ComparitorHandler[]
     */
    private static function getHandlersFromDirectory(string $path): array
    {
        return [
            new Equals,
            new Greater,
            new Less,
            new Both,
            new Either,
            new Regex,
            new Not,
            new Strict,
        ];
    }

    /**
     * @param  ComparitorHandler[]  $comparitors
     */
    private function buildStack(array $comparitors): void
    {
        if (empty($comparitors)) {
            return;
        }

        $this->handlerStack = $comparitors[0];
        $current = $this->handlerStack;

        for ($i = 1; $i < count($comparitors); $i++) {
            $current->next = $comparitors[$i];
            $current = $current->next;
        }
    }
}
