<?php
/**
 * CsvImportServiceProvider.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\ServiceProviders;

use Novelist\CsvImport\ImportHandler;

class CsvImportServiceProvider extends AbstractServiceProvider
{

    public function register()
    {
        // TODO: Implement register() method.
    }

    public function boot()
    {
        add_action('admin_init', function() {
            try {
                $this->container->get(ImportHandler::class)->handle();
            } catch(\Exception $e) {

            }
        });
    }
}
