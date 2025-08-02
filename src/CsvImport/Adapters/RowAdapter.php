<?php
/**
 * RowAdapter.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\CsvImport\Adapters;

use Exception;
use Novelist\DataObjects\Book;
use Novelist\DataObjects\RetailUrl;

class RowAdapter
{
    /**
     * Converts an array of CSV row data into a Book DTO.
     *
     * @throws Exception
     */
    public function convertToBook(array $data) : Book
    {
        $title = $data['title'] ?? null;
        if (empty($title)) {
            throw new Exception(__('Missing required title field.'));
        }

        return new Book(
            $this->getVisibility($data['visibility'] ?? null),
            $title,
            $this->getStringOrNull($data, 'series_name'),
            $this->getStringOrNull($data, 'series_position'),
            $this->getStringOrNull($data, 'cover'),
            $this->getStringOrNull($data, 'publish_date'),
            $this->getStringOrNull($data, 'publisher'),
            $this->getStringOrNull($data, 'synopsis'),
            (int) $this->getStringOrNull($data, 'page_count'),
            $this->getStringOrNull($data, 'isbn13'),
            $this->getStringOrNull($data, 'asin'),
            $this->parseCommaSeparatedStringsIntoArray($data, 'genres'),
            $this->getStringOrNull($data, 'goodreads_url'),
            $this->parseRetailers($data),
            $this->getStringOrNull($data, 'excerpt'),
            $this->getStringOrNull($data, 'extra_text'),
        );
    }

    protected function getVisibility(?string $visibility) : string
    {
        return in_array($visibility, ['publish', 'draft', 'private'], true) ? $visibility : 'publish';
    }

    protected function getStringOrNull(array $data, string $key) : ?string
    {
        $value = $data[$key] ?? null;

        return is_string($value) && ! empty($value) ? $value : null;
    }

    protected function parseCommaSeparatedStringsIntoArray(array $data, string $key) : array
    {
        $genreString = $data[$key] ?? null;
        if (empty($genreString)) {
            return [];
        }

        return array_map('trim', explode(',', $genreString));
    }

    protected function parseRetailers(array $data) : array
    {
        $retailers = [];

        foreach($data as $key => $value) {
            if (! $this->isRetailerKey($key)) {
                continue;
            }

            $storeId = str_replace('retailer_', '', $key);
            if (empty($storeId)) {
                continue;
            }

            $retailers[] = new RetailUrl(
                $storeId,
                $value
            );
        }

        return $retailers;
    }

    protected function isRetailerKey(string $key) : bool
    {
        return strpos($key, 'retailer_') === 0;
    }
}
