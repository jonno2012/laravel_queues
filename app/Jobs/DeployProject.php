<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeployProject implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public object $site;

    /**
     * Create a new job instance.
     */
    public function __construct($site, $latestCommitHash)
    {
        $this->site = $site;
        $this->latestCommitHash = $latestCommitHash;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app()->make('deployer')
            ->deploy(
                $this->latestCommitHash()
            );
    }
}
