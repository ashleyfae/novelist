<?php
/**
 * DownloadImageFromUrl.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\Actions;

use Exception;

class DownloadImageFromUrl
{
    protected int $attachedPostId = 0;

    /**
     * Sets the associated post ID.
     * If provided, the uploaded image will be associated with this object.
     */
    public function setAttachedPostId(int $attachedPostId): DownloadImageFromUrl
    {
        $this->attachedPostId = $attachedPostId;

        return $this;
    }

    /**
     * Downloads a remote image and uploads it to WP as an attachment.
     *
     * @throws Exception
     */
    public function execute(string $imageUrl, ?string $imageDescription = null): int
    {
        preg_match('/[^\?]+\.(jpe?g|jpe|gif|png|webp)\b/i', $imageUrl, $matches);
        if (empty($matches) || empty($matches[0])) {
            throw new Exception('Could not find image in URL');
        }

        // download file to /tmp
        $tempFileName = $this->downloadFile($imageUrl);

        // upload to WP as an attachment
        $attachmentId = media_handle_sideload([
            'name' => basename($matches[0]),
            'tmp_name' => $tempFileName,
        ], $this->attachedPostId, $imageDescription);

        if (file_exists($tempFileName)) {
            unlink($tempFileName);
        }

        if (is_wp_error($attachmentId)) {
            throw new Exception('Failed to upload file: '.$attachmentId->get_error_message());
        }

        return (int) $attachmentId;
    }

    /**
     * Downloads the file to a temporary directory.
     * @throws Exception
     */
    protected function downloadFile(string $imageUrl): string
    {
        if (! function_exists('download_url')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $tempFileName = download_url($imageUrl);
        if (is_wp_error($tempFileName)) {
            throw new Exception('Failed to download file: '.$tempFileName->get_error_message());
        }

        return $tempFileName;
    }
}
