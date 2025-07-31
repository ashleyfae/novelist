<?php
/**
 * bootstrap.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

const ABSPATH = 'foo/bar';

require_once dirname(__DIR__).'/vendor/autoload.php';

WP_Mock::setUsePatchwork( true);
WP_Mock::bootstrap();
