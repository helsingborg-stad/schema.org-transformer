<?php

namespace SchemaTransformer\LockRunner;

use PHPUnit\Framework\Attributes\TestDox;

class LockRunnerTest extends \PHPUnit\Framework\TestCase
{
    #[TestDox('does not allow multiple instances of the same lock to run concurrently')]
    public function testLockRunner()
    {
        $id         = 'test-lock-runner';
        $lockRunner = new LockRunner($id);
        $lockRunner->lock();

        $lockRunner2 = new LockRunner($id);
        $this->expectException(LockRunnerException::class);
        $this->expectExceptionMessage('Lock already acquired for ' . $id);

        $lockRunner2->lock();
    }

    #[TestDox('allows reuse of lock if the lock is released')]
    public function testLockRunnerRelease()
    {
        $id         = 'test-lock-runner-release';
        $lockRunner = new LockRunner($id);
        $lockRunner->lock();
        $lockRunner->release();

        $lockRunner2 = new LockRunner($id);
        $lockRunner2->lock();
        $lockRunner2->release();

        static::assertTrue(true, 'Lock was successfully acquired and released without exceptions.');
    }
}
