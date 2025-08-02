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
use Novelist\Helpers\Hooks;

class CsvImportServiceProvider extends AbstractServiceProvider
{

    public function register()
    {
    }

    public function boot()
    {
        Hooks::addAction('admin_init', ImportHandler::class, 'handle');
        Hooks::addAction('novelist/tools/tab/import_export', AdminPage::class, 'render');
    }
}
