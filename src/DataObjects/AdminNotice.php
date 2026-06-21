<?php
/**
 * AdminNotice.php
 *
 * @package   wp
 * @copyright Copyright (c) 2026, Ashley Gibson
 * @license   MIT
 */

namespace Novelist\DataObjects;

class AdminNotice
{
    public function __construct(
        public string $message,
        public string $class = ''
    ) {}
}
