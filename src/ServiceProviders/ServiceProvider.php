<?php
/**
 * ServiceProvider.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\ServiceProviders;

interface ServiceProvider
{
    /**
     * Registers the service provider within the application.
     *
     * @since 2.0
     *
     * @return void
     */
    public function register();

    /**
     * Bootstraps the service after all of the services have been registered.
     * All dependencies will be available at this point.
     *
     * @since 2.0
     *
     * @return void
     */
    public function boot();
}
