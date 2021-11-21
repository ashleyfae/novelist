<?php
/**
 * BooksInShortcode.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\Shortcodes;

class BooksInShortcode extends BookGridShortcode
{

    public static function tag(): string
    {
        return 'books-in';
    }

}
