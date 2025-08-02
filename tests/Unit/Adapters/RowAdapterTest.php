<?php
/**
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\Tests\Unit\Adapters;

use Exception;
use Novelist\CsvImport\Adapters\RowAdapter;
use Novelist\DataObjects\RetailUrl;
use Novelist\Tests\TestCase;

/**
 * @coversDefaultClass \Novelist\CsvImport\Adapters\RowAdapter
 */
final class RowAdapterTest extends TestCase
{
    /**
     * @see RowAdapter::convertToBook()
     * @throws Exception
     */
    public function testCanConvertToRow() : void
    {
        $data = [
            'title' => 'The Dragon\'s Path',
            'series_name' => 'The Fantasy Series',
            'series_position' => '1',
            'cover' => 'https://example.com/cover.jpg',
            'publish_date' => '2025-01-15',
            'publisher' => 'Fantasy Press',
            'synopsis' => 'A long synopsis with, commas, and "quotes" inside',
            'page_count' => '350',
            'isbn13' => '9781234567890',
            'asin' => 'B00EXAMPLE',
            'genres' => 'Fantasy, Adventure, Magic',
            'goodreads_url' => 'https://goodreads.com/book1',
            'retailer_amazon' => 'https://amazon.com/book1',
            'retailer_barnes' => 'https://barnesandnoble.com/book1',
            'retailer_kobo' => 'https://kobo.com/book1',
            'excerpt' => 'First chapter excerpt here...',
            'extra_text' => 'Additional notes here'
        ];

        $book = (new RowAdapter())->convertToBook($data);

        $this->assertSame('publish', $book->visibility);
        $this->assertSame('The Dragon\'s Path', $book->bookTitle);
        $this->assertSame('The Fantasy Series', $book->seriesName);
        $this->assertSame('1', $book->seriesPosition);
        $this->assertSame('https://example.com/cover.jpg', $book->coverUrl);
        $this->assertSame('2025-01-15', $book->publishDate);
        $this->assertSame('Fantasy Press', $book->publisher);
        $this->assertSame('A long synopsis with, commas, and "quotes" inside', $book->synopsis);
        $this->assertSame(350, $book->numberPages);
        $this->assertSame('9781234567890', $book->isbn13);
        $this->assertSame('B00EXAMPLE', $book->asin);
        $this->assertSame([
            'Fantasy',
            'Adventure',
            'Magic',
        ], $book->genreNames);
        $this->assertSame('https://goodreads.com/book1', $book->goodreadsUrl);
        $this->assertCount(3, $book->retailUrls);
        $this->assertSame('First chapter excerpt here...', $book->excerpt);
        $this->assertSame('Additional notes here', $book->extraText);

        // retailers
        $this->assertEquals([
            new RetailUrl('amazon', 'https://amazon.com/book1'),
            new RetailUrl('barnes', 'https://barnesandnoble.com/book1'),
            new RetailUrl('kobo', 'https://kobo.com/book1'),
        ], $book->retailUrls);
    }
}
