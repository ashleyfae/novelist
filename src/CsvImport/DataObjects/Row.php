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
    public string $visibility = 'publish';
    public string $bookTitle;
    public ?string $seriesName;
    public ?string $seriesPosition;
    public ?string $coverUrl;
    public ?string $publishDate;
    public ?string $publisher;
    public ?string $synopsis;
    public ?int $numberPages;
    public ?string $isbn13;
    public ?string $asin;
    public array $genreNames = [];
    public ?string $goodreadsUrl;
    /** @var RetailUrl[] */
    public array $retailUrls = [];
    public ?string $excerpt;
    public ?string $extraText;

    public function __construct(
        string $visibility,
        string $bookTitle,
        ?string $seriesName,
        ?string $seriesPosition,
        ?string $coverUrl,
        ?string $publishDate,
        ?string $publisher,
        ?string $synopsis,
        ?int $numberPages,
        ?string $isbn13,
        ?string $asin,
        array $genreNames,
        ?string $goodreadsUrl,
        array $retailUrls,
        ?string $excerpt,
        ?string $extraText
    ) {
        $this->visibility = $visibility;
        $this->bookTitle = $bookTitle;
        $this->seriesName = $seriesName;
        $this->seriesPosition = $seriesPosition;
        $this->coverUrl = $coverUrl;
        $this->publishDate = $publishDate;
        $this->publisher = $publisher;
        $this->synopsis = $synopsis;
        $this->numberPages = $numberPages;
        $this->isbn13 = $isbn13;
        $this->asin = $asin;
        $this->genreNames = $genreNames;
        $this->goodreadsUrl = $goodreadsUrl;
        $this->retailUrls = $retailUrls;
        $this->excerpt = $excerpt;
        $this->extraText = $extraText;
    }
}
