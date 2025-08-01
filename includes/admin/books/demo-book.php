<?php
/**
 * Functions for importing the demo book.
 *
 * @package   novelist
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

use Novelist\Actions\CreateBook;
use Novelist\DataObjects\Book;
use Novelist\DataObjects\RetailUrl;

function novelist_import_demo_book()
{
    check_ajax_referer('novelist_import_demo_book', 'nonce');

    if (! current_user_can('publish_books')) {
        wp_send_json_error(
            sprintf(
                __('You don\'t have permission to add %s', 'novelist'),
                novelist_get_label_plural(true)
            )
        );

        exit;
    }

    try {
        $book_id = Novelist()->container()->get(CreateBook::class)->execute(novelistGetDemoBook());
    } catch (Exception $e) {
        wp_send_json_error(__('Error: There was a problem adding the demo book.', 'novelist'));
        exit;
    }

    /*
     * Update option to designate that we've added the demo book.
     */
    update_option('novelist_imported_demo_book', true);

    /*
     * Action
     */
    do_action('novelist/demo-book/import', $book_id);

    $response = sprintf(
        '<a href="%1$s" class="button button-primary" target="_blank">%2$s</a> <a href="%3$s" class="button button-secondary" target="_blank">%4$s</a>',
        esc_url(get_permalink($book_id)),
        __('View Book', 'novelist'),
        esc_url(get_edit_post_link($book_id)),
        __('Edit Book', 'novelist')
    );

    wp_send_json_success($response);

    exit;
}

add_action('wp_ajax_novelist_import_demo_book', 'novelist_import_demo_book');

function novelistGetDemoBook(): Book
{
    $synopsis = __('Lorem ipsum dolor sit amet, consectetur adipiscing elit.

Aenean sagittis risus vel leo sodales, ut varius felis mattis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Vivamus convallis tortor diam, ac ultricies elit sodales id. Nunc vel vulputate neque. Cras sed semper nunc. Duis eleifend auctor feugiat. Suspendisse maximus et sem et semper. Fusce venenatis massa in ultricies maximus. Fusce pulvinar nisl quis tincidunt finibus. Nam euismod ipsum felis, ac vehicula ex condimentum sed. In tincidunt sapien at tellus sagittis, a convallis nibh vulputate. Donec vitae ex nec mauris porttitor imperdiet a in metus. Aliquam erat volutpat. Vestibulum libero risus, fringilla sed nisi eu, pellentesque ullamcorper ex. Mauris vel dapibus arcu.

Proin nisl enim, cursus ac felis in, tempus sodales velit. Donec et dolor nibh. Donec mauris magna, tincidunt sit amet massa ut, accumsan pulvinar arcu.', 'novelist');

    $extraText = __('Phasellus a eros tempus, scelerisque mi a, consectetur velit. Donec arcu augue, finibus nec cursus eu, posuere eu nisl.

Vivamus non dui ac enim aliquet dapibus. Nulla interdum auctor egestas. Duis interdum sapien id ipsum malesuada, eu porttitor felis rutrum. Curabitur laoreet hendrerit tristique. Pellentesque pretium, nulla eu finibus condimentum, nibh elit faucibus diam, nec volutpat arcu nunc eget ligula. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Quisque dictum justo erat, convallis pulvinar ex scelerisque at. Sed sit amet euismod nisl.', 'novelist');

    $saved_links = novelist_get_option('purchase_links', []);
    $retailUrls  = [];
    if (is_array($saved_links)) {
        foreach ($saved_links as $link) {
            $link_key = esc_attr(sanitize_title($link['name']));

            switch ($link_key) {
                case 'amazon' :
                    $url = 'https://www.amazon.com';
                    break;
                case 'barnes-noble' :
                    $url = 'https://www.barnesandnoble.com';
                    break;
                default :
                    $url = 'https://novelistplugin.com';
                    break;
            }

            $retailUrls[] = new RetailUrl(
                $link_key,
                $url,
            );
        }
    }

    return new Book(
        'private',
        __('Under a Forever Sky', 'novelist'),
        apply_filters('novelist/demo-book/series-name', __('Night Sky', 'novelist')),
        '1',
        NOVELIST_PLUGIN_URL.'assets/images/under-forever-sky.jpg',
        'February 18th 2016',
        __('Night Sky Publishing', 'novelist'),
        $synopsis,
        316,
        null,
        null,
        [
            apply_filters('novelist/demo-book/genre-name', __('Fantasy', 'novelist')),
        ],
        'https://www.goodreads.com',
        $retailUrls,
        null,
        $extraText
    );
}
