<?php
/**
 * Series Grid
 *
 * Displays a grid of books, grouped by series. This shortcode actually calls the
 * `[novelist-books]` shortcode and uses most of the same attributes.
 *
 * Attributes unique to this shortcode are:
 *      `series-name`
 *      `series-exclude`
 *      `standalones`
 *
 * @package   novelist
 * @copyright Copyright (c) 2021, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\Shortcodes;

class SeriesGridShortcode implements Shortcode
{

    public static function tag(): string
    {
        return 'novelist-series-grid';
    }

    public function make($atts, $content = ''): string
    {
        $atts = shortcode_atts(array(
            'series-name'   => 'true',
            // Whether or not to show the name of the series before the grid.
            'exclude'       => '',
            // Comma-separated list of series IDs or names to exclude.
            'standalones'   => 'true',
            // Whether or not to show standalones at the end.
            'columns'       => 4,
            // Number of columns in each row.
            'title'         => 'false',
            // Whether or not to display the title.
            'series-number' => 'false',
            // Whether or not to display the number in the series.
            'excerpt'       => 'false',
            // Excerpt from the synopsis.
            'full-content'  => 'false',
            // Fully formatted book content.
            'full-synopsis' => 'false',
            // Fully synopsis
            'button'        => 'true',
            // Button to single book page. Text in here becomes the button text.
            'covers'        => 'true',
            // Whether or not to display book cover.
            'covers-align'  => 'center',
            // Cover alignment.
            'orderby'       => 'series_number',
            // How to order the results. Options: `name`, `title`, `random`, `publication`, `date`, `menu_order`, `series_number`
            'order'         => 'ASC',
            // Order of the results.
            'offset'        => 0
            // Number of books to skip
        ), $atts, 'novelist-series-grid');

        // No pagination because WTF.
        $atts['pagination'] = 'false';
        $atts['number']     = -1;

        $series_args  = array(
            'taxonomy' => 'novelist-series',
        );
        $excluded_ids = array();

        $excluded_series = explode(',', $atts['exclude']);

        foreach ($excluded_series as $name) {
            if (is_numeric($name)) {
                $excluded_ids[] = $name;
            } else {
                $term = get_term_by('name', trim($name), 'novelist-series');

                if (! $term) {
                    continue;
                }

                $excluded_ids[] = $term->term_id;
            }
        }

        if (count($excluded_ids)) {
            $series_args['exclude'] = $excluded_ids;
        }

        $all_series = get_terms(apply_filters('novelist/shortcode/series-grid/get-terms-args', $series_args));
        $output     = '';

        /*
         * Display each series.
         */
        if ($all_series && is_array($all_series)) {

            foreach ($all_series as $series) {

                // Manually add some attributes.
                $this_series_atts           = $atts;
                $this_series_atts['series'] = $series->term_id;

                $output .= '<div class="novelist-series-grid">';

                // Display the series name
                if (filter_var($atts['series-name'], FILTER_VALIDATE_BOOLEAN)) {
                    $output .= apply_filters(
                        'novelist/shortcode/series-grid/series-name',
                        '<h2 id="series-'.esc_attr($series->slug).'">'.esc_html($series->name).'</h2>'
                    );
                }

                // Main grid shortcode.
                $output .= novelist(BookGridShortcode::class)->make($this_series_atts);

                $output .= '</div>';

            }

        }

        /*
         * Display standalones.
         */
        if (filter_var($atts['standalones'], FILTER_VALIDATE_BOOLEAN)) {
            $this_series_atts           = $atts;
            $this_series_atts['series'] = 'none';
            $standalone_grid            = novelist(BookGridShortcode::class)->make($this_series_atts);

            if (! empty($standalone_grid)) {
                $output .= '<div class="novelist-series-grid">';

                // Display the series name
                if (filter_var($atts['series-name'], FILTER_VALIDATE_BOOLEAN)) {
                    $output .= apply_filters(
                        'novelist/shortcode/series-grid/standalone-series-name',
                        '<h2>'.__('Standalones', 'novelist').'</h2>'
                    );
                }

                // Main grid shortcode.
                $output .= $standalone_grid;

                $output .= '</div>';
            }
        }

        return $output;
    }
}
