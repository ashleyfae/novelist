<?php
/**
 * RowAdapterTest.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\Tests\Unit\CsvImport\Adapters;

use Exception;
use Novelist\CsvImport\Adapters\RowAdapter;
use Novelist\CsvImport\DataObjects\RetailUrl;
use Novelist\Tests\TestCase;

/**
 * @coversDefaultClass \Novelist\CsvImport\Adapters\RowAdapter
 */
final class RowAdapterTest extends TestCase
{
    /**
     * @see RowAdapter::convertToRow()
     * @throws Exception
     */
    public function testCanConvertToRow() : void
    {
        $data = [
            'title' => 'The Dragon\'s Path',
            'series' => 'The Fantasy Series',
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

        $row = (new RowAdapter())->convertToRow($data);

        $this->assertSame('The Dragon\'s Path', $row->bookTitle);
        $this->assertSame('The Fantasy Series', $row->seriesName);
        $this->assertSame('1', $row->seriesPosition);
        $this->assertSame('https://example.com/cover.jpg', $row->coverUrl);
        $this->assertSame('2025-01-15', $row->publishDate);
        $this->assertSame('Fantasy Press', $row->publisher);
        $this->assertSame('A long synopsis with, commas, and "quotes" inside', $row->synopsis);
        $this->assertSame(350, $row->numberPages);
        $this->assertSame('9781234567890', $row->isbn13);
        $this->assertSame('B00EXAMPLE', $row->asin);
        $this->assertSame([
            'Fantasy',
            'Adventure',
            'Magic',
        ], $row->genreNames);
        $this->assertSame('https://goodreads.com/book1', $row->goodreadsUrl);
        $this->assertCount(3, $row->retailUrls);
        $this->assertSame('First chapter excerpt here...', $row->excerpt);
        $this->assertSame('Additional notes here', $row->extraText);

        // retailers
        $this->assertEquals([
            new RetailUrl('amazon', 'https://amazon.com/book1'),
            new RetailUrl('barnes', 'https://barnesandnoble.com/book1'),
            new RetailUrl('kobo', 'https://kobo.com/book1'),
        ], $row->retailUrls);
    }
}
