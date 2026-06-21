<?php
/**
 * LicenseFieldRenderer.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2026, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\Licensing;

use AshleyFae\SoftwareUpdater\DataTransferObjects\LicenseStatusResponse;
use AshleyFae\SoftwareUpdater\License\LicenseManager;
use AshleyFae\SoftwareUpdater\SDK;
use Exception;
use Novelist\DataObjects\AdminNotice;

class LicenseFieldRenderer
{
    public function render(
        string $fieldId,
        string $licenseKey,
        array $fieldArguments
    ) : void {
        $licenseManager = $this->getLicense($fieldId);
        $licenseStatus = $licenseManager?->getStatus();

        $wrapperClass = $licenseStatus ? $licenseStatus->status->getValue() : 'license-null';
        ?>
        <div class="<?php echo sanitize_html_class($wrapperClass); ?>">
            <input
                type="text"
                class="regular-text"
                id="novelist_settings[<?php echo novelist_sanitize_key($fieldId); ?>]"
                name="novelist_settings[<?php echo novelist_sanitize_key($fieldId); ?>]"
                value="<?php echo esc_attr($licenseKey); ?>"
            >
            <?php

            // License key is valid, so let's show a deactivate button.
            if ($licenseStatus && $licenseStatus->isActivatedFor(home_url())) {
                ?>
                <input
                    type="submit"
                    class="button-secondary"
                    name="<?php echo esc_attr($fieldId); ?>_deactivate"
                    value="<?php _e('Deactivate License', 'novelist'); ?>"
                >
                <?php
            }

            ?>
            <label for="novelist_settings[<?php echo novelist_sanitize_key($fieldId); ?>]" class="desc">
                <?php echo wp_kses_post($fieldArguments['desc']); ?>
            </label>
            <?php

            foreach ($this->getNotices($licenseKey, $licenseStatus) as $notice) {
                ?>
                <div class="novelist-license-data novelist-license-<?php echo sanitize_html_class($notice->class); ?> desc">
                    <p><?php echo wp_kses_post($notice->message); ?></p>
                </div>
                <?php
            }

            wp_nonce_field(novelist_sanitize_key($fieldId).'-nonce', novelist_sanitize_key($fieldId).'-nonce');
            ?>
        </div>
        <?php
    }

    protected function getLicense(string $optionName) : ?LicenseManager
    {
        if (! class_exists(SDK::class)) {
            return null;
        }

        try {
            return SDK::instance()->license($optionName);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @return AdminNotice[]
     */
    protected function getNotices(string $licenseKey, ?LicenseStatusResponse $licenseStatusResponse) : array
    {
        if ($licenseStatusResponse?->isExpired()) {
            return [
                $this->getExpiredLicenseNotice($licenseKey, $licenseStatusResponse),
            ];
        }

        if ($licenseStatusResponse?->isActive() && $licenseStatusResponse?->isActivatedFor(home_url())) {
            return [
                $this->getActiveLicenseNotice($licenseKey, $licenseStatusResponse),
            ];
        }

        return [];
    }

    protected function getExpiredLicenseNotice(string $licenseKey, LicenseStatusResponse $licenseStatusResponse): AdminNotice
    {
        return new AdminNotice(
            message: sprintf(
                __('Your license key expired on %1$s. Please <a href="%2$s" target="_blank" title="Renew your license key">renew your license key</a>.', 'novelist'),
                esc_html(date_i18n(get_option('date_format'), $licenseStatusResponse->expiresAt->getTimestamp())),
                esc_url('https://novelistplugin.com/checkout/?edd_license_key='.urlencode($licenseKey).'&utm_campaign=admin&utm_source=licenses&utm_medium=expired')
            ),
            class: 'error'
        );
    }

    protected function getActiveLicenseNotice(string $licenseKey, LicenseStatusResponse $licenseStatusResponse) : AdminNotice
    {
        $expiresAtTimestamp = $licenseStatusResponse->expiresAt?->getTimestamp();

        if (! $expiresAtTimestamp) {
            $message = __('License key never expires.', 'novelist');
        } elseif($this->licenseExpiresSoon($expiresAtTimestamp)) {
            $message = sprintf(
                __('Your license key expires on %1$s. <a href="%2$s" target="_blank" title="Renew license key">Renew your license key</a> to continue getting updates and support.', 'novelist'),
                date_i18n(get_option('date_format'), $expiresAtTimestamp),
                'https://novelistplugin.com/checkout/?edd_license_key='.urlencode($licenseKey).'&utm_campaign=admin&utm_source=licenses&utm_medium=renew'
            );
        } else {
            $message = sprintf(
                __('Your license key expires on %s.', 'novelist'),
                date_i18n(get_option('date_format'), $expiresAtTimestamp)
            );
        }

        return new AdminNotice(
            message: $message,
            class: 'valid'
        );
    }

    /**
     * Determines if the license expires within 30 days.
     */
    protected function licenseExpiresSoon(int $expiresAt) : bool
    {
        $now = time();

        return $expiresAt > $now &&
            (($expiresAt - $now) < (DAY_IN_SECONDS * 30));
    }
}
