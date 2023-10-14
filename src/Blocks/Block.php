<?php
/**
 * Block.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\Blocks;

interface Block
{

    /**
     * Registers the block with WordPress. This should call `register_block_type()`.
     *
     * @return void
     */
    public function register();

}
