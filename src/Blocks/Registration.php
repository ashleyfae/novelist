<?php
/**
 * Registration.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\Blocks;

class Registration
{
    /**
     * Blocks to register
     *
     * @var string[]
     */
    private $blocks = [];

    public function __invoke()
    {
        foreach ($this->blocks as $block) {
            if (! is_subclass_of($block, Block::class)) {
                throw new \InvalidArgumentException(sprintf(
                    'The %s class must implement the %s interface.',
                    $block,
                    Block::class
                ));
            }

            /** @var Block $block */
            $block = new $block();
            $block->register();
        }
    }

}
