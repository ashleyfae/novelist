<?php
/**
 * Plugin Name: Novelist
 * Plugin URI: https://novelistplugin.com
 * Description: Easily organize and display your portfolio of books
 * Version: 1.3.0
 * Author: Nose Graze
 * Author URI: https://www.nosegraze.com
 * License: GPL2
 * Text Domain: novelist
 * Domain Path: languages
 * Requires at least: 5.0
 * Requires PHP: 7.4
 *
 * Novelist is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Novelist is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Novelist. If not, see <http://www.gnu.org/licenses/>.
 *
 * Thanks to Easy Digital Downloads for serving as a great code base
 * and resource, which a lot of Novelist's structure is based on.
 * Easy Digital Downloads is made by Pippin Williamson and licensed
 * under GPL2+.
 *
 * @package   novelist
 * @copyright Copyright (c) 2025 Nose Graze Ltd
 * @license   GPL2+
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

if (version_compare(PHP_VERSION, '7.4', '<')) {
    return;
}

require __DIR__.'/vendor/autoload.php';

if (! class_exists('Novelist')) :

    class Novelist
    {
        private \Novelist\Container\Container $container;

        private static Novelist $instance;

        /**
         * Service providers to boot.
         *
         * @see Novelist::boot()
         * @see Novelist::loadServiceProviders()
         *
         * @var string[]
         */
        private array $serviceProviders = [
            \Novelist\ServiceProviders\CsvImportServiceProvider::class,
        ];

        private bool $serviceProvidersLoaded = false;

        /**
         * Novelist_Roles object
         *
         * @var Novelist_Roles
         * @since 1.0.0
         */
        public Novelist_Roles $roles;

        /**
         * HTML elements helper class.
         *
         * @var Novelist_HTML
         * @since 1.1.0
         */
        public Novelist_HTML $html;

        public function __construct()
        {
            $this->container = new \Novelist\Container\Container();
        }

        /**
         * Initializes the plugin.
         *
         * @since 2.0
         */
        public function boot()
        {
            $this->setup_constants();
            $this->loadServiceProviders();

            add_action('plugins_loaded', [$this, 'load_textdomain']);

            $this->includes();
            $this->roles = new Novelist_Roles();
            $this->html  = new Novelist_HTML();
        }

        /**
         * Novelist instance.
         *
         * Insures that only one instance of Novelist exists at any one time.
         *
         * @uses   Novelist::setup_constants() Set up the plugin constants.
         * @uses   Novelist::includes() Include any required files.
         * @uses   Novelist::load_textdomain() Load the language files.
         *
         * @access public
         * @since  1.0.0
         * @return Novelist Instance of Novelist class
         */
        public static function instance() : Novelist
        {
            if (! isset(static::$instance) || ! static::$instance instanceof Novelist) {
                static::$instance = new static();
            }

            return static::$instance;
        }

        /**
         * Gets the container
         *
         * @since 2.0
         *
         * @return \Novelist\Container\Container
         */
        public function container() : \Novelist\Container\Container
        {
            return $this->container;
        }

        /**
         * Throw error on object clone.
         *
         * The whole idea of the singleton design pattern is that there is a single
         * object therefore, we don't want the object to be cloned.
         *
         * @access protected
         * @since  1.0.0
         * @return void
         */
        public function __clone()
        {
            // Cloning instances of the class is forbidden.
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'novelist'), '1.0.0');
        }

        /**
         * Disable unserializing of the class.
         *
         * @access protected
         * @since  1.0.0
         * @return void
         */
        public function __wakeup()
        {
            // Unserializing instances of the class is forbidden.
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'novelist'), '1.0.0');
        }

        /**
         * Setup plugin constants.
         *
         * @access private
         * @since  1.0.0
         * @return void
         */
        private function setup_constants()
        {
            // Plugin version.
            if (! defined('NOVELIST_VERSION')) {
                define('NOVELIST_VERSION', '1.3.0');
            }

            // Plugin Folder Path.
            if (! defined('NOVELIST_PLUGIN_DIR')) {
                define('NOVELIST_PLUGIN_DIR', plugin_dir_path(__FILE__));
            }

            // Plugin Folder URL.
            if (! defined('NOVELIST_PLUGIN_URL')) {
                define('NOVELIST_PLUGIN_URL', plugin_dir_url(__FILE__));
            }

            // Plugin Root File.
            if (! defined('NOVELIST_PLUGIN_FILE')) {
                define('NOVELIST_PLUGIN_FILE', __FILE__);
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
                if (! is_subclass_of($serviceProvider, \Novelist\ServiceProviders\ServiceProvider::class)) {
                    throw new \InvalidArgumentException(sprintf(
                        '%s class must implement the ServiceProvider interface.',
                        $serviceProvider
                    ));
                }

                /** @var \Novelist\ServiceProviders\ServiceProvider $serviceProvider */
                $serviceProvider = new $serviceProvider($this->container);
                $serviceProvider->register();
                $providers[] = $serviceProvider;
            }

            foreach ($providers as $serviceProvider) {
                $serviceProvider->boot();
            }

            $this->serviceProvidersLoaded = true;
        }

        /**
         * Include Required Files
         *
         * @access private
         * @since  1.0.0
         * @return void
         */
        private function includes()
        {

            global $novelist_options;

            // Settings.
            require_once NOVELIST_PLUGIN_DIR.'includes/admin/settings/register-settings.php';
            if (empty($novelist_options)) {
                $novelist_options = novelist_get_settings();
            }

            require_once NOVELIST_PLUGIN_DIR.'includes/class-novelist-html.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/class-novelist-roles.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/post-types.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/class-novelist-book.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/class-novelist-shortcodes.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/book-functions.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/book-filters.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/load-assets.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/misc-functions.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/template-functions.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/widgets/widget-book.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/widgets/widget-books-by-series.php';
            require_once NOVELIST_PLUGIN_DIR.'includes/widgets/widget-word-count.php';

            if (is_admin()) {
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/admin-actions.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/extensions.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/thickbox.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/tools.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/admin-pages.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/class-novelist-notices.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/class-welcome.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/settings/display-settings.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/books/meta-box.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/books/sanitize-meta-fields.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/books/dashboard-columns.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/books/demo-book.php';
                require_once NOVELIST_PLUGIN_DIR.'includes/admin/upgrades/upgrade-functions.php';
            }

            require_once NOVELIST_PLUGIN_DIR.'includes/install.php';

        }

        /**
         * Loads the plugin language files.
         *
         * @access public
         * @since  1.0.0
         * @return void
         */
        public function load_textdomain()
        {
            $lang_dir = dirname(plugin_basename(NOVELIST_PLUGIN_FILE)).'/languages/';
            $lang_dir = apply_filters('novelist/languages-directory', $lang_dir);
            load_plugin_textdomain('novelist', false, $lang_dir);
        }

    }

endif; // End class exists check.

/**
 * Get Novelist up and running.
 *
 * This function returns an instance of the Novelist class.
 *
 * @since 1.0.0
 * @return Novelist
 */
function Novelist()
{
    return Novelist::instance();
}

Novelist()->boot();
