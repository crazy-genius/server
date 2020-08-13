<?php

declare(strict_types=1);

namespace App\HTTP;

class Server
{
    private string $host;
    private int $port;

    /** @var resource */
    private $socket;

    public function __construct(string $host, int $port = 80)
    {
        $this->host = $host;
        $this->port = $port;
        $this->createSocket();
    }

    private function createSocket(): void
    {
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($this->socket === false) {
            throw new \RuntimeException('Ошибка создания сокета');
        }
    }

    public function start(): void
    {
        $socket = $this->socket;
        socket_bind($socket, $this->host, $this->port);
        socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
        socket_listen($socket);

        while ($connect = socket_accept($socket)) {
            $response = new Response('Привет');
            $message = $response->toString();
            socket_write($connect, $message, strlen($message));
            socket_close($connect);
        }
    }

    public function stop(): void
    {
        fclose($this->socket);
    }
}
