<?php
/**
 * Hooks.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\Helpers;

class Hooks
{

    /**
     * Wrapper for `add_action()` to handle the instantiation of a class only once the
     * action is fired. This prevents the need to instantiate a class before adding it to hook.
     *
     * Taken from GiveWP.
     *
     * @since 2.0
     *
     * @param  string  $tag
     * @param  string  $class
     * @param  string  $method
     * @param  int  $priority
     * @param  int  $acceptedArgs
     */
    public static function addAction(
        string $tag,
        string $class,
        string $method = '__invoke',
        int $priority = 10,
        int $acceptedArgs = 1
    ) {
        if (! method_exists($class, $method)) {
            throw new \InvalidArgumentException(sprintf(
                'The method %s does not exist on %s.',
                $method,
                $class
            ));
        }

        add_action(
            $tag,
            static function () use ($tag, $class, $method) {
                call_user_func_array([novelist($class), $method], func_get_args());
            },
            $priority,
            $acceptedArgs
        );
    }

    /**
     * Wrapper for `add_filter()` to handle the instantiation of a class only once the
     * action is fired. This prevents the need to instantiate a class before adding it to hook.
     *
     * Taken from GiveWP.
     *
     * @since 2.0
     *
     * @param  string  $tag
     * @param  string  $class
     * @param  string  $method
     * @param  int  $priority
     * @param  int  $acceptedArgs
     */
    public static function addFilter(
        string $tag,
        string $class,
        string $method = '__invoke',
        int $priority = 10,
        int $acceptedArgs = 1
    ) {
        if (! method_exists($class, $method)) {
            throw new \InvalidArgumentException(sprintf(
                'The method %s does not exist on %s.',
                $method,
                $class
            ));
        }

        add_filter(
            $tag,
            static function () use ($tag, $class, $method) {
                return call_user_func_array([novelist($class), $method], func_get_args());
            },
            $priority,
            $acceptedArgs
        );
    }

}
