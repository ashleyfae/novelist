<?php
/**
 * Hooks.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\Helpers;

use InvalidArgumentException;

class Hooks
{
    public static function addAction(
        string $tag,
        string $class,
        string $method = '__invoke',
        int $priority = 10,
        int $acceptedArgs = 1
    ) {
        if (! method_exists($class, $method)) {
            throw new InvalidArgumentException(sprintf('Method %s does not exist on class %s', $method, $class));
        }

        add_action(
            $tag,
            static function () use ($tag, $class, $method) {
                $instance = Novelist()->container()->get($class);

                $instance->$method(func_get_args());
            },
            $priority,
            $acceptedArgs
        );
    }

    public static function addFilter(
        string $tag,
        string $class,
        string $method = '__invoke',
        int $priority = 10,
        int $acceptedArgs = 1
    ) {
        if (! method_exists($class, $method)) {
            throw new InvalidArgumentException(sprintf('Method %s does not exist on class %s', $method, $class));
        }

        add_filter(
            $tag,
            static function () use ($tag, $class, $method) {
                $instance = Novelist()->container()->get($class);

                $instance->$method(func_get_args());
            },
            $priority,
            $acceptedArgs
        );
    }
}
