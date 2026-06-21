<?php
/**
 * AdminNotice.php
 *
 * @package   novelist
 * @copyright Copyright (c) 2026, Ashley Gibson
 * @license   GPL2+
 */

namespace Novelist\DataObjects;

class AdminNotice
{
    public function __construct(
        public string $message,
        public string $class = ''
    ) {}
}
