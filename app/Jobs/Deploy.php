<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\ThrottlesExceptions;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class Deploy implements ShouldQueue, ShouldBeUnique
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
        // here the lock is named 'deployment'
//        Cache::lock('deployments')->block(10, function() { // lock is laravel's lock race method
//            info('starting deployment');
//
//            sleep(5);
//
//            info('finishing deployment');
//
//        });

//        Redis::funnel('deployments') // the redis concurrency limited can be used instead and is better than the lock methods
//        ->limit(5) // only 5 instances of te same job can run at the same time
//            ->block(10) // block for 10 seconds while it waits for a lock to be acquired. if no lock acquired, it will fail.
//            ->then(function () {
//                info('statred deploying');
//
//                sleep(5);
//
//                info('finished deployment');
//            });

        Redis::throttle('deployments') // the best way of handling rate limiting
            ->allow(10)
            ->every(60)
            ->block(10)
            ->then(function () {
                info('startinf deployment...');
                sleep(5);
                info('finishing deployment...');
            });
    }

    public function middleware()
    {
//        return [
//            // will release the job back to the queue after 10 seconds if there is another instance of the job in progress
//            new WithoutOverlapping('deployments', 10)
//        ];

        return [
            // acts as circuit breaker if jobs are failing too often. can be used to, for example, stop afailed api calls
            // stopping our system from working.
            new ThrottlesExceptions()
        ];
    }

//    public function uniqueId() // a custom key name for the unique job key in the db. key defaults to class name
//    {
//        return 'deployments';
//    }
//
//    public function uniqueFor() // if ShouldBeUnique is implemented and for some reason a job gets stuck, it will
//        // prevent any further instances of the same job running. here we can tell it to remove lock after a given time period
//        // to prevent any issues with the job from stopping any further instances from running.
//    {
//        return 60;
//    }
}
