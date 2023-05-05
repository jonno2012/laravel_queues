<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//    public $timeout = 1;
        public $tries = 3;
//    public $backoff = [2, 10]; // will try 2 secs after first then 10 after second etc
//    public $maxExceptions = 5;
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
        sleep(3);
        $this->release(2); // pushes job back to the queue to be retried after 2 secs. this will override the backoff value if set.
    }

//    public function retryUntil()
//    {
//        return now()->addMinute();
//    }

    public function failed(\Exception $e)
    {
        info($e->getMessage());
    }
}
