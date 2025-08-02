<?php
/**
 * AdminPage.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\CsvImport;

class AdminPage
{
    public const REQUIRED_CAPABILITY = 'manage_novelist_settings';
    public const NONCE_ACTION = 'novelist_import_book_csv';
    public const NONCE_NAME = 'novelist_import_book_csv_nonce';

    protected array $importedBookIds = [];
    protected array $importErrorMessages = [];

    protected function getTransientArray(string $transientName): array
    {
        $value = get_transient($transientName);

        if (empty($value)) {
            return [];
        }

        return (array) $value;
    }

    public function render(): void
    {
        if (! current_user_can(static::REQUIRED_CAPABILITY)) {
            return;
        }

        $this->importedBookIds     = $this->getTransientArray('novelist_csv_imported_books');
        $this->importErrorMessages = $this->getTransientArray('novelist_csv_import_errors');
        ?>
        <div class="postbox">
            <h3><span><?php _e('Import Books', 'novelist'); ?></span></h3>
            <div class="inside">
                <p>
                    <?php
                    echo wp_kses(
                        sprintf(
                        /* translators: %s file URL */
                            __(
                                'Import books from a CSV file. The file must be formatted with the appropriate headers, as shown in the <a href="%s">sample CSV file</a>. Title is the only required field; all others are optional.',
                                'novelist'
                            ),
                            esc_url(NOVELIST_PLUGIN_URL.'/assets/csv/example.csv')
                        ),
                        [
                            'a' => ['href' => true],
                        ]
                    );
                    ?>
                </p>
                <p>
                    <?php
                    echo wp_kses(
                        sprintf(
                        /* translators: %s admin page URL */
                            __('Retailer column headers must be prefixed with "retail_", followed by the store ID from <a href="%s">the retailers page</a> (e.g. retail_amazon).',
                                'novelist'),
                            esc_url(admin_url('edit.php?post_type=book&page=novelist-settings&tab=book&section=purchase-links'))
                        ),
                        [
                            'a' => ['href' => true],
                        ]
                    );
                    ?>
                </p>
                <p>
                    <?php esc_html_e('If you have many books to upload (hundreds) then it may be better to split the file into multiple smaller ones and upload one at a time.', 'novelist'); ?>
                </p>
                <?php
                if (! empty($this->importErrorMessages)) {
                    $this->renderErrorMessages($this->importErrorMessages);
                }
                if (! empty($this->importedBookIds)) {
                    $this->renderImportedBooks($this->importedBookIds);
                }
                ?>
                <form method="POST" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('edit.php?post_type=book&page=novelist-tools&tab=import_export')); ?>">
                    <p><input type="file" name="import_file"></p>
                    <p>
                        <?php wp_nonce_field(static::NONCE_ACTION, static::NONCE_NAME); ?>
                        <?php submit_button(__('Import', 'novelist'), 'secondary', 'submit', false); ?>
                    </p>
                </form>
            </div>
        </div>
        <?php
    }

    protected function renderErrorMessages(array $messages): void
    {
        echo '<div class="notice notice-error inline">';
        echo '<p>'.esc_html__('The following errors occurred while importing the CSV file:', 'novelist').'</p>';

        echo '<ul>';
        foreach ($messages as $message) {
            echo '<li>' . esc_html($message) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    protected function renderImportedBooks(array $bookIds): void
    {
        $books = get_posts([
            'post_status' => 'any',
            'post_type' => 'book',
            'post__in' => $bookIds,
        ]);

        if (empty($books)) {
            return;
        }

        echo '<div class="notice notice-success inline">';

        echo '<p>'.esc_html__('The following books were imported successfully:', 'novelist').'</p>';

        echo '<ul>';
        foreach ($books as $book) {
            echo '<li><a href="'.esc_url(get_edit_post_link($book)).'">' . esc_html(get_the_title($book)) . '</a></li>';
        }
        echo '</ul>';

        echo '</div>';
    }
}
