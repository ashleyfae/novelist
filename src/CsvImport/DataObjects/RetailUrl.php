<?php
/**
 * RetailUrl.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2025, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\CsvImport\DataObjects;

class RetailUrl
{
    public string $storeId;
    public string $url;

    public function __construct( string $storeId, string $url )
    {
        $this->storeId = $storeId;
        $this->url     = $url;
    }
}
