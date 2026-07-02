<?php

namespace SchemaTransformer\Loggers;

use Psr\Log\LoggerInterface;
use Stringable;

class TerminalLogger implements LoggerInterface
{
    public function __construct(private string $flag = '')
    {
    }

    public function emergency(string|Stringable $message, array $context = []): void
    {
        $this->log('emergency', $message, $context);
    }

    public function alert(string|Stringable $message, array $context = []): void
    {
        $this->log('alert', $message, $context);
    }

    public function critical(string|Stringable $message, array $context = []): void
    {
        $this->log('critical', $message, $context);
    }

    public function error(string|Stringable $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }

    public function warning(string|Stringable $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function notice(string|Stringable $message, array $context = []): void
    {
        $this->log('notice', $message, $context);
    }

    public function info(string|Stringable $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function debug(string|Stringable $message, array $context = []): void
    {
        $this->log('debug', $message, $context);
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $timestamp = "[" . $this->getTimestamp() . "]";
        $flag      = !empty($this->flag) ? "[$this->flag]" : '';
        $level     = "[" . strtoupper($level) . "]";

        echo "{$timestamp}{$flag}{$level}: $message" . PHP_EOL;
    }

    private function getTimestamp(): string
    {
        return date('Y-m-d H:i:s');
    }
}
