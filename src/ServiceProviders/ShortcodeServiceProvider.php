<?php
/**
 * ShortcodeServiceProvider.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\ServiceProviders;

use Novelist\Shortcodes;

class ShortcodeServiceProvider implements ServiceProvider
{
    protected $shortcodes = [
        Shortcodes\BooksInShortcode::class,
        Shortcodes\BookGridShortcode::class,
        Shortcodes\SeriesGridShortcode::class,
        Shortcodes\ShowBookShortcode::class,
    ];

    public function register(): void
    {

    }

    public function boot(): void
    {
        foreach ($this->shortcodes as $shortcode) {
            if (is_subclass_of($shortcode, Shortcodes\Shortcode::class)) {
                /** @var Shortcodes\Shortcode $shortcode */
                $shortcode = novelist()->make($shortcode);
                add_shortcode($shortcode::tag(), [$shortcode, 'make']);
            }
        }
    }
}
