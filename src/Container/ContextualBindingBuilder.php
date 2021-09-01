<?php
/**
 * ContextualBindingBuilder.php
 *
 * Taken from Laravel.
 *
 * @link      https://github.com/laravel/framework/blob/3bde8d5e3cf412c1e885eb310522b51d0053f736/src/Illuminate/Container/ContextualBindingBuilder.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\Container;

class ContextualBindingBuilder
{

    /**
     * The underlying container instance.
     *
     * @var Container
     */
    protected $container;

    /**
     * The concrete instance.
     *
     * @var array
     */
    protected $concrete;

    /**
     * The abstract target.
     *
     * @var string
     */
    protected $needs;

    /**
     * ContextualBindingBuilder constructor.
     *
     * @param Container    $container
     * @param array|string $concrete
     */
    public function __construct( Container $container, $concrete ) {
        $this->container = $container;
        $this->concrete  = is_array( $concrete ) ? $concrete : [ $concrete ];
    }

    /**
     * Define the abstract target that depends on the context.
     *
     * @param string $abstract
     *
     * @return $this
     */
    public function needs( $abstract ) {
        $this->needs = $abstract;

        return $this;
    }

    /**
     * Define the implementation for the contextual binding.
     *
     * @param \Closure|string|array $implementation
     *
     * @return void
     */
    public function give( $implementation ) {
        foreach ( $this->concrete as $concrete ) {
            $this->container->addContextualBinding( $concrete, $this->needs, $implementation );
        }
    }

}
