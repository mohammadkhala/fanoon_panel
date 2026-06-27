<?php

namespace App\Logging;

use Monolog\Logger;

/**
 * Redacts sensitive data (OTP, tokens, passwords) from log context to prevent accidental exposure.
 */
class RedactSensitiveData
{
    private const SENSITIVE_KEYS = ['otp', 'token', 'password', 'reset_token', 'secret', 'api_key'];

    public function __invoke($logger): void
    {
        if (!$logger instanceof Logger) {
            return;
        }
        $logger->pushProcessor(function (array $record): array {
            if (isset($record['context']) && is_array($record['context'])) {
                foreach (self::SENSITIVE_KEYS as $key) {
                    if (array_key_exists($key, $record['context'])) {
                        $record['context'][$key] = '[REDACTED]';
                    }
                }
            }
            return $record;
        });
    }
}
