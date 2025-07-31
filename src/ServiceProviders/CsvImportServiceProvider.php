<?php
/**
 * CsvImportServiceProvider.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\ServiceProviders;

class CsvImportServiceProvider implements ServiceProvider
{

    public function register()
    {
        // TODO: Implement register() method.
    }

    public function boot()
    {
        add_action('admin_init', function() {
            
        });
    }
}
