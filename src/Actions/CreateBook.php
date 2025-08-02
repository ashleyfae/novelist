<?php
/**
 * BookCreator.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\Actions;

use Exception;
use Novelist\DataObjects\Book;
use WP_Term;

class CreateBook
{
    protected DownloadImageFromUrl $downloadImageFromUrl;

    public function __construct(DownloadImageFromUrl $downloadImageFromUrl)
    {
        $this->downloadImageFromUrl = $downloadImageFromUrl;
    }

    /**
     * Inserts a new book with corresponding data.
     *
     * @throws Exception
     */
    public function execute(Book $book) : int
    {
        $bookData = [
            'post_title' => $book->bookTitle,
            'post_status' => $book->visibility,
            'post_type' => 'book',
            'meta_input' => $this->getBookMeta($book),
        ];

        $bookId = wp_insert_post($bookData);
        if (! $bookId) {
            throw new Exception('Failed to insert book into the database.');
        }

        if (is_wp_error($bookId)) {
            throw new Exception('Failed to insert book into the database: ' . $bookId->get_error_message());
        }

        $this->handleBookTerms((int) $bookId, $book);

        return (int) $bookId;
    }

    protected function getBookMeta(Book $row) : array
    {
        return array_filter(
            [
                'novelist_title' => $row->bookTitle ? sanitize_text_field($row->bookTitle) : null,
                'novelist_cover' => $this->getBookCoverId($row->coverUrl),
                'novelist_series' => $row->seriesPosition ? sanitize_text_field($row->seriesPosition) : null,
                'novelist_publisher' => $row->publisher ? sanitize_text_field($row->publisher) : null,
                'novelist_pub_date' => $row->publishDate ? sanitize_text_field($row->publishDate) : null,
                'novelist_pub_date_timestamp' => $row->publishDate ? strtotime($row->publishDate) : null,
                'novelist_pages' => $row->numberPages ? (int) $row->numberPages : null,
                'novelist_isbn' => $row->isbn13 ? sanitize_text_field($row->isbn13) : null,
                'novelist_asin' => $row->asin ? sanitize_text_field($row->asin) : null,
                'novelist_synopsis' => $row->synopsis ? novelist_wp_kses_post($row->synopsis) : null,
                'novelist_goodreads' => $row->goodreadsUrl ? esc_url_raw($row->goodreadsUrl) : null,
                'novelist_purchase_links' => $this->getPurchaseLinksMeta($row->retailUrls),
                'novelist_excerpt' => $row->excerpt ? novelist_wp_kses_post($row->excerpt) : null,
                'novelist_extra' => $row->extraText ? novelist_wp_kses_post($row->extraText) : null,
            ]
        );
    }

    protected function getBookCoverId(?string $coverUrl) : ?int
    {
        if (empty($coverUrl)) {
            return null;
        }

        $attachmentId = attachment_url_to_postid($coverUrl);
        if (! empty($attachmentId)) {
            return $attachmentId;
        }

        try {
            return $this->downloadImageFromUrl->execute($coverUrl);
        } catch(Exception $e) {
            return null;
        }
    }

    protected function getPurchaseLinksMeta(array $retailUrls) : ?array
    {
        if (empty($retailUrls)) {
            return null;
        }

        $meta = [];
        foreach($retailUrls as $retailUrl) {
            $meta[sanitize_title($retailUrl->storeId)] = esc_url_raw($retailUrl->url);
        }

        return $meta;
    }

    protected function handleBookTerms(int $bookId, Book $row) : void
    {
        if ($row->seriesName) {
            $this->setSeries($bookId, $row->seriesName);
        }

        if (! empty($row->genreNames)) {
            $this->setGenres($bookId, $row->genreNames);
        }
    }

    protected function setSeries(int $bookId, string $seriesName) : void
    {
        try {
            $this->setTermOnBook($bookId, $seriesName, 'novelist-series');
        } catch(Exception $e) {

        }
    }

    protected function setGenres(int $bookId, array $genreNames) : void
    {
        foreach($genreNames as $genreName) {
            try {
                $this->setTermOnBook($bookId, $genreName, 'novelist-genre');
            } catch(Exception $e) {

            }
        }
    }

    /**
     * @throws Exception
     */
    protected function setTermOnBook(int $bookId, string $termName, string $taxonomy) : void
    {
        $termId = $this->getOrCreateTerm($termName, $taxonomy);

        wp_set_post_terms($bookId, [$termId], $taxonomy);
    }

    /**
     * @return int term ID
     * @throws Exception
     */
    protected function getOrCreateTerm(string $termName, string $taxonomy) : int
    {
        $term = get_term_by('name', $termName, $taxonomy);
        if ($term instanceof WP_Term) {
            return $term->term_id;
        }

        $termInfo = wp_insert_term($termName, $taxonomy);
        if (is_wp_error($termInfo)) {
            throw new Exception('Error creating term: ' . $termInfo->get_error_message());
        }

        return (int) $termInfo['term_id'];
    }
}
