<?php

namespace Asundust\Helpers\Support;

class Alog
{
    /**
     * Alog constructor
     *
     * @param string $name
     * @param string $path
     * @param int $days
     * @param string $driver
     * @param array $configs
     * @param string $channel
     */
    public function __construct(
        protected string $name = 'custom',
        protected string $path = 'custom',
        protected int $days = 14,
        protected string $driver = 'daily',
        protected array $configs = [],
        protected string $channel = 'custom'
    ) {
    }

    public function emergency(...$message): void
    {
        $this->log('emergency', $message);
    }

    public function alert(...$message): void
    {
        $this->log('alert', $message);
    }

    public function critical(...$message): void
    {
        $this->log('critical', $message);
    }

    public function error(...$message): void
    {
        $this->log('error', $message);
    }

    public function warning(...$message): void
    {
        $this->log('warning', $message);
    }

    public function notice(...$message): void
    {
        $this->log('notice', $message);
    }

    public function info(...$message): void
    {
        $this->log('info', $message);
    }

    public function debug(...$message): void
    {
        $this->log('debug', $message);
    }

    protected function log($level, $message): void
    {
        $channelConfig = config('logging.channels.daily', []);
        config([
            "logging.channels.$this->channel" => array_merge($channelConfig, [
                'driver' => $this->driver,
                'path' => storage_path('logs/' . $this->path . '/' . $this->name . '.log'),
                'days' => $this->days,
            ], $this->configs)
        ]);
        $fileInfo = '';
        if (function_exists('debug_backtrace')) {
            $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            if (isset($traces[1]) && $trace = $traces[1]) {
                if (is_array($trace) && isset($trace['file']) && isset($trace['line'])) {
                    $file = substr(str_replace(base_path(), '', $trace['file']), 1);
                    $fileInfo = "{$file}:{$trace['line']}";
                }
            }
        }
        if (is_array($message) && count($message) == 1) {
            $message = $message[0];
        }
        if (is_array($message)) {
            logger()->channel($this->channel)->$level($fileInfo);
            logger()->channel($this->channel)->$level($message);
        } else {
            logger()->channel($this->channel)->$level($fileInfo . PHP_EOL . $message);
        }
    }
}
