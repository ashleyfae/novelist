<?php
/**
 * LegacyServiceProvider.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\ServiceProviders;

use Novelist\Helpers\Html;
use Novelist\Plugin;

class LegacyServiceProvider implements ServiceProvider
{

    public function register(): void
    {
        $this->includeFiles();
        $this->registerClassAliases();
        $this->bindClasses();
    }

    public function boot(): void
    {

    }

    private function includeFiles(): void
    {
        global $novelist_options;

        // Settings.
        require_once NOVELIST_PLUGIN_DIR.'includes/admin/settings/register-settings.php';
        if (empty($novelist_options)) {
            $novelist_options = novelist_get_settings();
        }

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

    private function registerClassAliases(): void
    {
        class_alias(Plugin::class, 'Novelist');
        class_alias(Html::class, 'Novelist_HTML');
    }

    /**
     * Binds classes that used to be properties in the `Novelist` class. (now `Novelist\Plugin`)
     */
    private function bindClasses(): void
    {
        novelist()->bind(\Novelist_Roles::class);
        novelist()->alias(\Novelist_Roles::class, 'roles');

        novelist()->bind(Html::class);
        novelist()->alias(Html::class, 'html');
    }
}
