<?php
/**
 * BookCreator.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\CsvImport;

use Exception;
use Novelist\CsvImport\DataObjects\Row;
use WP_Term;

class BookCreator
{
    public function insertFromRow(Row $row) : void
    {
        $bookData = [
            'post_title' => $row->bookTitle,
            'post_status' => 'publish', // @TODO
            'post_type' => 'book',
            'meta_input' => $this->getBookMeta($row),
        ];

        $bookId = wp_insert_post($bookData);
        if ($bookId && ! is_wp_error($bookId)) {
            $this->handleBookTerms((int) $bookId, $row);
        }
    }

    protected function getBookMeta(Row $row) : array
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

        // @TODO handle uploads
        return null;
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

    protected function handleBookTerms(int $bookId, Row $row) : void
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
