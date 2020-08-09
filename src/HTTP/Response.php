<?php

declare(strict_types=1);

namespace App\HTTP;

class Response
{
    private array $headers;
    private string $message;

    public function __construct(string $message)
    {
        $this->headers = [];
        $this->headers[] = 'HTTP/1.1 200 OK';
        $this->headers[] = 'Content-Type: text/html; charset=utf-8';
        $this->headers[] = 'Connection: close';

        $this->message = $message;
    }

    public function toString(): string
    {
        return implode("\r\n", $this->headers) . sprintf("\r\n\r\n%s", $this->message);
    }
}
