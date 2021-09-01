<?php
/**
 * Blocks.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\ServiceProviders;

use Novelist\Blocks\Registration;
use Novelist\Helpers\Hooks;

class Blocks implements ServiceProvider
{

    public function register()
    {
        // TODO: Implement register() method.
    }

    public function boot()
    {
        Hooks::addAction('init', Registration::class);
    }
}
