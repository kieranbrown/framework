<?php

namespace Illuminate\Queue;

use RuntimeException;

class SqsBulkDispatchException extends RuntimeException
{
    /**
     * Create a new exception instance.
     *
     * @param  array<int, array{job: \Closure|string|object|null, code: string|null, message: string|null}>  $failedJobs
     * @param  array<int, \Throwable>  $exceptions
     */
    public function __construct(
        public readonly array $failedJobs = [],
        public readonly array $exceptions = [],
    ) {
        parent::__construct(sprintf(
            'Bulk SQS dispatch failed: [%d] entries were rejected and [%d] batch requests failed.',
            count($failedJobs),
            count($exceptions),
        ), 0, $exceptions[0] ?? null);
    }
}
