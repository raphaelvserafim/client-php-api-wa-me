<?php

/**
 * Test runner — plain PHP, no dependencies.
 * Usage: php tests/run.php
 */

require __DIR__ . '/bootstrap.php';

require __DIR__ . '/webhook_meta_test.php';
require __DIR__ . '/provider_send_test.php';
require __DIR__ . '/wame_alias_test.php';

exit(test_summary());
