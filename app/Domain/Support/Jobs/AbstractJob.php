<?php

namespace App\Domain\Support\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

abstract class AbstractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        try {
            $this->init();
            $this->execute();
        } catch (Throwable $exception) {
            $this->handleException($exception);

            throw $exception;
        }
    }

    public function init() {}

    public abstract function execute();

    public abstract function handleException(Throwable $exception);
}
