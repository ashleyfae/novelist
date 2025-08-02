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
use Novelist\Actions\CreateBook;
use Novelist\CsvImport\Adapters\RowAdapter;

class ImportHandler
{
    protected RowAdapter $rowAdapter;
    protected CreateBook $bookCreator;

    public function __construct(
        RowAdapter $rowAdapter,
        CreateBook $bookCreator
    ) {
        $this->rowAdapter  = $rowAdapter;
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

        delete_transient('novelist_csv_imported_books');
        delete_transient('novelist_csv_import_errors');

        try {
            $csv = $this->getCsvAsArray();
            if (empty($csv)) {
                throw new Exception(__('No CSV data found.', 'novelist'));
            }

            $booksImported = $this->processCsv($csv);

            wp_safe_redirect(
                add_query_arg(
                    [
                        'novelist-message'        => 'books-imported',
                        'novelist-books-imported' => count($booksImported),
                    ],
                    admin_url('edit.php?post_type=book&page=novelist-tools&tab=import_export')
                )
            );
        } catch (Exception $e) {
            wp_die($e->getMessage());
        }
    }

    protected function isUploadRequest(): bool
    {
        return ! empty($_POST[AdminPage::NONCE_NAME]) && ! empty($_FILES['import_file']);
    }

    protected function isUserAuthorised(): bool
    {
        return wp_verify_nonce($_POST[AdminPage::NONCE_NAME], AdminPage::NONCE_ACTION) &&
            current_user_can(AdminPage::REQUIRED_CAPABILITY);
    }

    /**
     * @throws Exception
     */
    protected function getCsvAsArray(): array
    {
        $filePath = $_FILES['import_file']['tmp_name'] ?? null;
        if (! $filePath) {
            throw new Exception(__('CSV file not found.', 'novelist'));
        }

        return $this->parseCsv($filePath);
    }

    /**
     * @throws Exception
     */
    public function parseCsv(string $filepath): array
    {
        if (! file_exists($filepath)) {
            throw new Exception(__('CSV file not found.', 'novelist'));
        }

        $handle = fopen($filepath, 'r');
        if ($handle === false) {
            return [];
        }

        $data = [];

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

    protected function processCsv(array $rows): array
    {
        $insertedBookIds = [];
        $errors          = [];

        foreach ($rows as $rowData) {
            try {
                $insertedBookIds[] = $this->bookCreator->execute(
                    $this->rowAdapter->convertToBook($rowData)
                );
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (! empty($insertedBookIds)) {
            set_transient('novelist_csv_imported_books', $insertedBookIds, 10 * MINUTE_IN_SECONDS);
        }

        if (! empty($errors)) {
            set_transient('novelist_csv_import_errors', $errors, 10 * MINUTE_IN_SECONDS);
        }

        return $insertedBookIds;
    }
}
