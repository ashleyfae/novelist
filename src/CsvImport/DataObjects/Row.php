<?php
/**
 * Row.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\CsvImport\DataObjects;

class Row
{
    public string $bookTitle;
    public ?string $seriesName;
    public ?string $coverUrl;
    public ?string $publishDate;
    public ?string $publisher;
    public ?string $synopsis;
    public ?int $numberPages;
    public ?string $isbn13;
    public ?string $asin;
    public array $genreNames = [];
    public ?string $goodreadsUrl;

    public function __construct(
        string $bookTitle,
        ?string $seriesName,
        ?string $coverUrl,
        ?string $publishDate,
        ?string $publisher,
        ?string $synopsis,
        ?int $numberPages,
        ?string $isbn13,
        ?string $asin,
        array $genreNames,
        ?string $goodreadsUrl
    ) {
        $this->bookTitle = $bookTitle;
        $this->seriesName = $seriesName;
        $this->coverUrl = $coverUrl;
        $this->publishDate = $publishDate;
        $this->publisher = $publisher;
        $this->synopsis = $synopsis;
        $this->numberPages = $numberPages;
        $this->isbn13 = $isbn13;
        $this->asin = $asin;
        $this->genreNames = $genreNames;
        $this->goodreadsUrl = $goodreadsUrl;
    }
}
