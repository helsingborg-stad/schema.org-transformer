<?php

namespace SchemaTransformer\LockRunner;

use Override;
use PHPUnit\Framework\Attributes\TestDox;
use Stringable;

class LockRunnerTest extends \PHPUnit\Framework\TestCase
{
    #[TestDox('does not allow multiple instances of the same lock to run concurrently')]
    public function testLockRunner()
    {
        $logger = static::getLogger();
        $id     = 'test-lock-runner';

        $lockRunner = new LockRunner($id, $logger);
        $lockRunner->lock();

        $lockRunner2 = new LockRunner($id, $logger);
        $lockRunner2->lock();

        static::assertCount(1, $logger->logMessages);
        static::assertSame($logger->logMessages[0]['level'], 'warning');
        static::assertSame($logger->logMessages[0]['message'], 'Lock already acquired for ' . $id);
    }

    #[TestDox('allows reuse of lock if the lock is released')]
    public function testLockRunnerRelease()
    {
        $logger     = static::getLogger();
        $id         = 'test-lock-runner-release';
        $lockRunner = new LockRunner($id, $logger);
        $lockRunner->lock();
        $lockRunner->release();

        $lockRunner2 = new LockRunner($id, $logger);
        $lockRunner2->lock();
        $lockRunner2->release();

        static::assertCount(0, $logger->logMessages);
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
