<?php
/**
 * TestCase.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\Tests;

use Novelist\Container\Container;

abstract class TestCase extends \WP_Mock\Tools\TestCase
{
    protected Container $container;

    public function setUp(): void
    {
        parent::setUp();

        $this->container = new Container();
    }
}
