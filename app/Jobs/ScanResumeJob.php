<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScanResumeJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public \App\Models\VagaCandidatura $candidatura)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(\App\Services\AiResumeScannerService $scanner): void
    {
        $scanner->scan($this->candidatura);
    }

}
