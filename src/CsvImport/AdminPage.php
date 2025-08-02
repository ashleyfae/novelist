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

    public function render(): void
    {
        if (! current_user_can(static::REQUIRED_CAPABILITY)) {
            return;
        }

        ?>
        <div class="postbox">
            <h3><span><?php _e('Import Books', 'novelist'); ?></span></h3>
            <div class="inside">
                <p>
                    <?php
                    echo wp_kses(
                        sprintf(
                            /* translators: %s file URL */
                            __('Import books from a CSV file. The file must be formatted with the appropriate headers, as shown in the <a href="%s">sample CSV file</a>. Title is the only required field; all others are optional.', 'novelist'),
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
                            __('Retailer column headers must be prefixed with "retail_", followed by the store ID from <a href="%s">the retailers page</a> (e.g. retail_amazon).', 'novelist'),
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
}
