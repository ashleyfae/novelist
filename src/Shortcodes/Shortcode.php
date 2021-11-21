<?php
/**
 * Shortcode.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 * @since     2.0
 */

namespace Novelist\Shortcodes;

interface Shortcode
{

    /**
     * Shortcode tag name.
     *
     * @since 2.0
     *
     * @return string
     */
    public static function tag(): string;

    /**
     * Callback for rendering the shortcode.
     *
     * @since 2.0
     *
     * @param  array  $atts
     * @param  string  $content
     *
     * @return string
     */
    public function make($atts, $content = ''): string;

}
