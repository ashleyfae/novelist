<?php
/**
 * CsvImportServiceProvider.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\ServiceProviders;

use Exception;
use Novelist\CsvImport\AdminPage;
use Novelist\CsvImport\ImportHandler;

class CsvImportServiceProvider extends AbstractServiceProvider
{

    public function register()
    {
    }

    public function boot()
    {
        add_action('admin_init', function () {
            try {
                $this->container->get(ImportHandler::class)->handle();
            } catch (Exception $e) {

            }
        });

        add_action('novelist/tools/tab/import_export', function () {
            try {
                $this->container->get(AdminPage::class)->render();
            } catch (Exception $e) {

            }
        });
    }
}
