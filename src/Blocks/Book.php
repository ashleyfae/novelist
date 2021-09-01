<?php
/**
 * Book.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\Blocks;

class Book implements Block
{

    public function register()
    {
        register_block_type(
            'novelist/book',
            [
                'editor_script' => 'novelist-blocks',
            ]
        );
    }
}
