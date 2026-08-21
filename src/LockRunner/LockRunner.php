<?php

namespace SchemaTransformer\LockRunner;

use Psr\Log\LoggerInterface;

/**
 * Coordinates a process lock identified by a shared lock file.
 *
 * Provides a mechanism to prevent multiple instances of a process from running concurrently by acquiring an exclusive lock on a temporary file.
 * Used to prevent multiple instances of the same script from running at the same time, which could lead to data corruption or other unintended side effects.
 */
class LockRunner
{
    /**
     * Active lock handle retained for the lifetime of the lock.
     *
     * @var resource|null
     */
    private $lockHandle = null;

    /**
     * @param string $id Unique lock identifier.
     */
    public function __construct(
        private string $id,
        private LoggerInterface $logger = new \SchemaTransformer\Loggers\NullLogger()
    ) {
    }

    /**
     * Acquire an exclusive non-blocking lock.
     *
     * @return bool True if the lock was successfully acquired, false if the lock is already held by another process.
     *
     * @throws \RuntimeException If the lock is already acquired by this instance or if the lock file cannot be opened.
     */
    public function lock(): true
    {
        if ($this->lockHandle !== null) {
            throw new \RuntimeException("Lock already acquired for " . $this->id);
        }

        $lockFile = $this->getLockFilePath();
        $fp       = fopen($lockFile, 'c');
        if (!$fp) {
            throw new \RuntimeException("Unable to open lock file: $lockFile");
        }

        if (!flock($fp, LOCK_EX | LOCK_NB)) {
            fclose($fp);
            throw new \RuntimeException("Lock already acquired for " . $this->id);
        }

        $this->lockHandle = $fp;
        $this->logger->info("Lock acquired for " . $this->id);
        return true;
    }

    /**
     * Release the current lock if it is held by this instance.
     */
    public function release(): void
    {
        if ($this->lockHandle === null) {
            return;
        }

        flock($this->lockHandle, LOCK_UN);
        fclose($this->lockHandle);
        $this->lockHandle = null;
    }

    /**
     * @return string Absolute lock file path.
     */
    private function getLockFilePath(): string
    {
        return sys_get_temp_dir() . "/lockrunner_{$this->id}.lock";
    }
}
