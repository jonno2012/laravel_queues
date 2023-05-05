<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PullRepo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // when job inside batch fails the whole batch will be marked as cancelled. to ensure the job doesn't run
        // if the job was cancelled we need to add the following check:
        if($this->batch()->canceled()) {
            return;
        }

        sleep(2);
    }

    public function failed(\Throwable $excepttion)
    {
//        Log::log(1, $excepttion->getMessage());
    }
}
