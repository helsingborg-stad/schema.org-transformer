<?php

namespace SchemaTransformer\LockRunner;

use PHPUnit\Framework\Attributes\TestDox;
use Stringable;

class LockRunnerTest extends \PHPUnit\Framework\TestCase
{
    #[TestDox('throws when lock is already acquired')]
    public function testLockRunner()
    {
        $logger = static::getLogger();
        $id     = 'test-lock-runner';

        $lockRunner = new LockRunner($id, $logger);
        $lockRunner->lock();

        try {
            $lockRunner2 = new LockRunner($id, $logger);
            $lockRunner2->lock();
        } catch (\RuntimeException $e) {
            static::assertSame('Lock already acquired for ' . $id, $e->getMessage());
            return;
        }

        static::fail('Expected RuntimeException was not thrown');
    }

    #[TestDox('allows reuse of lock if the lock is released')]
    public function testLockRunnerRelease()
    {
        $logger     = static::getLogger();
        $id         = 'test-lock-runner-release';
        $lockRunner = new LockRunner($id, $logger);
        $lockRunner->lock();
        $lockRunner->release();

        try {
            $lockRunner2 = new LockRunner($id, $logger);
            $lockRunner2->lock();
            $lockRunner2->release();
        } catch (\RuntimeException $e) {
            static::fail('Unexpected RuntimeException was thrown: ' . $e->getMessage());
        }

        static::assertTrue(true, 'Lock was successfully acquired and released without exceptions.');
    }

    private static function getLogger(): \Psr\Log\LoggerInterface
    {
        return new class extends \SchemaTransformer\Loggers\NullLogger {
            public array $logMessages = [];

            public function warning(string|Stringable $message, array $context = []): void
            {
                $this->logMessages[] = ['level' => 'warning', 'message' => (string)$message, 'context' => $context];
            }
        };
    }
}
