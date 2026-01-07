<?php

declare(strict_types=1);

namespace App\RuleEngine\Comparitors;

use DirectoryIterator;

class ComparitorStack
{
    private const string HANDLER_DIRECTORY = __DIR__ . '/Handlers';

    private const string HANDLER_NAMESPACE = 'App\\RuleEngine\\Comparitors\\Handlers\\';

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
        if (self::$instance) {
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
        $handlers = [];

        foreach (new DirectoryIterator($path) as $file) {
            if ($file->isDot() || $file->getExtension() !== 'php') {
                continue;
            }

            $className = self::HANDLER_NAMESPACE . $file->getBasename('.php');

            if (is_subclass_of($className, ComparitorHandler::class)) {
                $handlers[] = new $className();
            }
        }

        return $handlers;
    }

    /**
     * @param  ComparitorHandler[]  $comparitors
     */
    private function buildStack(array $comparitors): void
    {
        $this->handlerStack = array_shift($comparitors);
        $current = $this->handlerStack;

        while ($handler = array_shift($comparitors)) {
            $current->next = $handler;
            $current = $handler;
        }
    }
}
