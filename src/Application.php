<?php

declare(strict_types=1);

namespace App;

use App\HTTP\Server;

/**
 * Class Application
 * @package App
 */
class Application
{
    private array $deferred;

    public function __construct()
    {
        register_shutdown_function(static function () {
            echo '' . PHP_EOL;
        });
    }

    public function run(): int
    {
        $server = new Server('127.0.01', 5000);
        $this->defer(fn() => $server->stop());
        $server->start();

        return 0;
    }

    private function defer(callable $callback): void
    {
        $this->deferred[] = $callback;
    }

    private function printLn(string $message): void
    {
        echo $message . PHP_EOL;
    }

    public function __destruct()
    {
        foreach ($this->deferred as $callback) {
            if (is_callable($callback)) {
                $callback();
            }
        }
    }
}
