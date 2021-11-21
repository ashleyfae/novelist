<?php
/**
 * Show Book
 *
 * Displays the fully rendered book information for a single book.
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\Shortcodes;

class ShowBookShortcode implements Shortcode
{

    public static function tag(): string
    {
        return 'show-book';
    }

    public function make($atts, $content = ''): string
    {
        $atts = shortcode_atts(array(
            'title' => null, // Title of the book
            'id'    => null, // ID of the book
        ), $atts, 'show-book');

        $args = array(
            'post_type'      => 'book',
            'posts_per_page' => 1,
        );

        if (! empty($atts['id'])) {
            $args['p'] = intval($atts['id']);
        } elseif (! empty($atts['title'])) {
            $args['s'] = strip_tags($atts['title']);
        }

        $book_query = new \WP_Query(apply_filters('novelist/shortcode/show-book/query-args', $args));

        // No results - bail.
        if (! $book_query->have_posts()) {
            return __('[show-book] Error: No book found with this title.', 'novelist');
        }

        ob_start();

        while ($book_query->have_posts()) : $book_query->the_post();

            do_action('novelist/shortcode/show-book/before-content', get_the_ID(), $atts);

            novelist_get_template_part('book', 'content');

            do_action('novelist/shortcode/show-book/after-content', get_the_ID(), $atts);

        endwhile;

        wp_reset_postdata();

        return ob_get_clean();
    }
}
