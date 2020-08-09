<?php

declare(strict_types=1);

namespace App;

/**
 * Class Application
 * @package App
 */
class Application
{
    private array $deferred;

    public function __construct() {
        register_shutdown_function(static function () {
            echo '' . PHP_EOL;
        });
    }

    public function run(): int
    {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($socket === false) {
            $this->printLn('Ошибка создания сокета');
            return 255;
        }

        $this->defer(fn () => fclose($socket));

        socket_bind($socket, '127.0.0.1', 4000);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_listen($socket);

        while ($connect = socket_accept($socket)) {
            socket_write($connect, "HTTP/1.1 200 OK\r\nContent-Type: text/html\r\nConnection: close\r\n\r\nПривет");
            socket_close($connect);
        }

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
