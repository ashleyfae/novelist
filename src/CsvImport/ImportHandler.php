<?php
/**
 * ImportHandler.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\CsvImport;

use Exception;
use Novelist\CsvImport\Adapters\RowAdapter;

class ImportHandler
{
    protected RowAdapter $rowAdapter;
    protected BookCreator $bookCreator;

    public function __construct(
        RowAdapter $rowAdapter,
        BookCreator $bookCreator
    ) {
        $this->rowAdapter = $rowAdapter;
        $this->bookCreator = $bookCreator;
    }

    public function handle(): void
    {
        if (! $this->isUploadRequest()) {
            return;
        }

        if (! $this->isUserAuthorised()) {
            wp_die(__('You do not have permission to perform this action.', 'novelist'));
        }

        try {
            $csv = $this->getCsvAsArray();
            if (empty($csv)) {
                throw new Exception(__('No CSV data found.', 'novelist'));
            }

            $this->processCsv($csv);
        } catch (Exception $e) {
            wp_die($e->getMessage());
        }
    }

    protected function isUploadRequest(): bool
    {
        return false; // @ TODO
    }

    protected function isUserAuthorised(): bool
    {
        // @TODO nonce check
        return current_user_can('manage_options');
    }

    protected function getCsvAsArray(): array
    {
        $filePath = $_FILES['csv_file']['tmp_name'] ?? null;
        if (! $filePath) {
            throw new \Exception(__('CSV file not found.', 'novelist'));
        }

        return $this->parseCsv($filePath);
    }

    public function parseCsv(string $filepath): array
    {
        if (! file_exists($filepath)) {
            throw new \Exception(__('CSV file not found.', 'novelist'));
        }

        $handle = fopen($filepath, 'r');
        if ($handle === false) {
            return [];
        }

        $headers = [];
        $data    = [];

        // Get headers from first row
        if (($headers = fgetcsv($handle)) !== false) {
            // Read the rest of the rows
            while (($row = fgetcsv($handle)) !== false) {
                // Make sure row has same number of columns as headers
                $min    = min(count($headers), count($row));
                $data[] = array_combine(
                    array_slice($headers, 0, $min),
                    array_slice($row, 0, $min)
                );
            }
        }

        fclose($handle);

        return $data;
    }

    protected function processCsv(array $rows): void
    {
        foreach($rows as $rowData) {
            try {
                $this->bookCreator->insertFromRow(
                    $this->rowAdapter->convertToRow($rowData)
                );
            } catch(\Exception $e) {
                
            }
        }
    }
}
