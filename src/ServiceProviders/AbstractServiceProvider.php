<?php
/**
 * AbstractServiceProvider.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\ServiceProviders;

use Novelist\Container\Container;

abstract class AbstractServiceProvider implements ServiceProvider
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}
