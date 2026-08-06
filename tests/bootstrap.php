<?php

/**
 * Minimal test bootstrap: PSR-4 autoloader for Api\Wame\ + a tiny assert
 * helper, so the suite runs with plain `php` (no Composer/PHPUnit needed).
 */

spl_autoload_register(function (string $class): void {
    $prefix = 'Api\\Wame\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $file = __DIR__ . '/../src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

$GLOBALS['__tests'] = ['pass' => 0, 'fail' => 0, 'fails' => []];

function ok(bool $cond, string $message): void
{
    if ($cond) {
        $GLOBALS['__tests']['pass']++;
    } else {
        $GLOBALS['__tests']['fail']++;
        $GLOBALS['__tests']['fails'][] = $message;
        fwrite(STDERR, "  ✗ {$message}\n");
    }
}

function eq($expected, $actual, string $message): void
{
    $cond = $expected === $actual;
    if (!$cond) {
        $message .= ' (expected ' . var_export($expected, true) . ', got ' . var_export($actual, true) . ')';
    }
    ok($cond, $message);
}

function test_summary(): int
{
    $t = $GLOBALS['__tests'];
    echo "\n{$t['pass']} passed, {$t['fail']} failed\n";
    return $t['fail'] === 0 ? 0 : 1;
}

/**
 * HttpClient double that records the last request instead of hitting the wire.
 * Message type-hints the concrete HttpClient, so we subclass it.
 */
class FakeHttpClient extends \Api\Wame\HttpClient
{
    /** @var array{method:string,path:string,body:?array,query:array}|null */
    public ?array $last = null;

    public function __construct()
    {
        parent::__construct('http://test.local', 'testkey');
    }

    public function request(string $method, string $path, ?array $body = null, array $query = []): ?string
    {
        $this->last = ['method' => $method, 'path' => $path, 'body' => $body, 'query' => $query];
        return json_encode(['status' => 200]);
    }
}
