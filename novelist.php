<?php
/**
 * Plugin Name: Novelist
 * Plugin URI: https://novelistplugin.com
 * Description: Easily organize and display your portfolio of books
 * Version: 1.1.11
 * Author: Nose Graze
 * Author URI: https://www.nosegraze.com
 * License: GPL2
 * Text Domain: novelist
 * Domain Path: languages
 * Requires at least: 5.0
 * Requires PHP: 7.1
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
 * @copyright Copyright (c) 2021 Nose Graze Ltd
 * @license   GPL2+
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

if (version_compare(phpversion(), '7.1', '<')) {
    return;
}

const NOVELIST_VERSION = '1.1.11';
const NOVELIST_PLUGIN_FILE = __FILE__;

require __DIR__.'/vendor/autoload.php';

/**
 * Get Novelist up and running.
 *
 * This function returns an instance of the Novelist class.
 *
 * @since 1.0.0
 * @return \Novelist\Plugin|object
 */
function novelist($abstract = null)
{
    static $instance = null;

    if ($instance === null) {
        $instance = new \Novelist\Plugin();
    }

    if ($abstract !== null) {
        return $instance->make($abstract);
    }

    return $instance;
}

novelist()->boot();
