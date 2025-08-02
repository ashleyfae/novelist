<?php
/**
 * ImportHandlerTest.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\Tests\Unit\CsvImport;

use Exception;
use Novelist\CsvImport\ImportHandler;
use Novelist\Tests\TestCase;

/**
 * @coversDefaultClass \Novelist\CsvImport\ImportHandler
 */
final class ImportHandlerTest extends TestCase
{
    protected string $demoFilePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->demoFilePath = __DIR__ . '/../../Fixtures/csv-import.csv';
    }

    /**
     * @see ImportHandler::parseCsv()
     * @throws Exception
     */
    public function testCanParseCsv() : void
    {
        $data = $this->container->get(ImportHandler::class)->parseCsv($this->demoFilePath);

        $this->assertCount(3, $data);

        $this->assertSame('The Dragon\'s Path', $data[0]['title']);
        $this->assertSame('The Fantasy Series', $data[0]['series_name']);
        $this->assertSame('1', $data[0]['series_position']);
        $this->assertSame('https://example.com/cover.jpg', $data[0]['cover']);
        $this->assertSame('2025-01-15', $data[0]['publish_date']);
        $this->assertSame('Fantasy Press', $data[0]['publisher']);
        $this->assertSame('A long synopsis with, commas, and "quotes" inside', $data[0]['synopsis']);
        $this->assertSame('350', $data[0]['page_count']);
        $this->assertSame('9781234567890', $data[0]['isbn13']);
        $this->assertSame('B00EXAMPLE', $data[0]['asin']);
        $this->assertSame('Fantasy, Adventure, Magic', $data[0]['genres']);
        $this->assertSame('https://goodreads.com/book1', $data[0]['goodreads_url']);
        $this->assertSame('https://amazon.com/book1', $data[0]['retailer_amazon']);
        $this->assertSame('https://barnesandnoble.com/book1', $data[0]['retailer_barnes']);
        $this->assertSame('https://kobo.com/book1', $data[0]['retailer_kobo']);
        $this->assertSame('First chapter excerpt here...', $data[0]['excerpt']);
        $this->assertSame('Additional notes here', $data[0]['extra_text']);
    }
}
