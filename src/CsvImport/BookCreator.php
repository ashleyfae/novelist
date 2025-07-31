<?php
/**
 * BookCreator.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\CsvImport;

use Novelist\CsvImport\DataObjects\Row;

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
    }

    protected function getBookMeta(Row $row) : array
    {
        $meta = array_filter(
            [
                'novelist_title' => $row->bookTitle,
                'novelist_series' => 0, // @TODO handle series
                'novelist_publisher' => $row->publisher,
                'novelist_pub_date' => $row->publishDate,
                'novelist_pub_date_timestamp'
            ]
        );
    }
}
