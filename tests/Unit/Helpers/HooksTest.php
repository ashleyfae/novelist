<?php
/**
 * HooksTest.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\Tests\Unit\Helpers;

use Closure;
use Novelist\Helpers\Hooks;
use Novelist\Tests\TestCase;
use WP_Mock;
use WP_Mock\Functions;

/**
 * @coversDefaultClass \Novelist\Helpers\Hooks
 */
final class HooksTest extends TestCase
{
    public function testCanAddAction() : void
    {
        WP_Mock::expectActionAdded('novelist_hook', Functions::type(Closure::class));

        Hooks::addAction('novelist_hook', ConcreteHookImplementation::class, 'callback', 10, 1);

        $this->assertConditionsMet();
    }

    public function testCanAddActionThrows() : void
    {
        $this->expectException(\InvalidArgumentException::class);

        WP_Mock::expectActionNotAdded('novelist_hook', Functions::type(Closure::class));;

        Hooks::addAction('novelist_hook', ConcreteHookImplementation::class, 'invalid');
    }

    public function testCanAddFilter() : void
    {
        WP_Mock::expectFilterAdded('novelist_hook', Functions::type(Closure::class));

        Hooks::addFilter('novelist_hook', ConcreteHookImplementation::class, 'callback', 10, 1);

        $this->assertConditionsMet();
    }

    public function testCanAddFilterThrows() : void
    {
        $this->expectException(\InvalidArgumentException::class);

        WP_Mock::expectFilterNotAdded('novelist_hook', Functions::type(Closure::class));;

        Hooks::addFilter('novelist_hook', ConcreteHookImplementation::class, 'invalid');
    }
}

class ConcreteHookImplementation
{
    public function callback()
    {

    }
}
