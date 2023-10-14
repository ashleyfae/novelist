<?php
/**
 * Plugin.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

namespace Novelist;

use Novelist\ServiceProviders;

/**
 * @since 2.0 Class renamed.
 */
class Plugin
{

    /** @var Container\Container */
    private $container;

    /**
     * Service providers to boot.
     *
     * @see Novelist::boot()
     * @see Novelist::loadServiceProviders()
     *
     * @var string[]
     */
    private $serviceProviders = [
        ServiceProviders\LegacyServiceProvider::class,
        ServiceProviders\ShortcodeServiceProvider::class,
        ServiceProviders\WidgetServiceProvider::class,
    ];

    /** @var bool */
    private $serviceProvidersLoaded = false;

    public function __construct()
    {
        $this->container = new Container\Container();
    }

    public function boot(): void
    {
        $this->setupConstants();
        $this->loadServiceProviders();

        add_action('plugins_loaded', [$this, 'load_textdomain']);
    }

    /**
     * Novelist instance.
     *
     * Insures that only one instance of Novelist exists at any one time.
     *
     * @deprecated 2.0
     *
     * @access public
     * @since  1.0.0
     * @return Plugin Instance of Novelist class
     */
    public static function instance(): Plugin
    {
        return novelist();
    }

    /**
     * Properties are loaded from the service container.
     *
     * @since 2.0
     *
     * @param  string  $property
     *
     * @return mixed|object
     * @throws \Exception
     */
    public function __get($property)
    {
        return $this->container->get($property);
    }

    /**
     * Magic methods are passed to the service container.
     *
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->container, $name], $arguments);
    }

    private function setupConstants(): void
    {
        // Plugin Folder Path.
        if (! defined('NOVELIST_PLUGIN_DIR')) {
            define('NOVELIST_PLUGIN_DIR', plugin_dir_path(NOVELIST_PLUGIN_FILE));
        }

        // Plugin Folder URL.
        if (! defined('NOVELIST_PLUGIN_URL')) {
            define('NOVELIST_PLUGIN_URL', plugin_dir_url(NOVELIST_PLUGIN_FILE));
        }
    }

    /**
     * Registers and boots all service providers.
     *
     * @since 2.0
     */
    private function loadServiceProviders()
    {
        if ($this->serviceProvidersLoaded) {
            return;
        }

        $providers = [];
        foreach ($this->serviceProviders as $serviceProvider) {
            if (! is_subclass_of($serviceProvider, ServiceProviders\ServiceProvider::class)) {
                throw new \InvalidArgumentException(sprintf(
                    '%s class must implement the ServiceProvider interface.',
                    $serviceProvider
                ));
            }

            /** @var ServiceProviders\ServiceProvider $serviceProvider */
            $serviceProvider = new $serviceProvider();
            $serviceProvider->register();
            $providers[] = $serviceProvider;
        }

        foreach ($providers as $serviceProvider) {
            $serviceProvider->boot();
        }

        $this->serviceProvidersLoaded = true;
    }

    /**
     * Loads the plugin language files.
     *
     * @access public
     * @since  1.0.0
     * @return void
     */
    public function load_textdomain(): void
    {
        $lang_dir = dirname(plugin_basename(NOVELIST_PLUGIN_FILE)).'/languages/';
        $lang_dir = apply_filters('novelist/languages-directory', $lang_dir);
        load_plugin_textdomain('novelist', false, $lang_dir);
    }

}
