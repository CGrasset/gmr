<?php

namespace App\Observers;

use App\Job;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class JobObserver
{
    /**
     * Handle the job "created" event.
     *
     * @param  \App\Job  $job
     * @return void
     */
    public function created(Job $job)
    {
        // Save to cache after creating
        $job->refresh();
        $this->putCache($job);
    }

    /**
     * Handle the job "updated" event.
     *
     * @param  \App\Job  $job
     * @return void
     */
    public function updated(Job $job)
    {
        // Save/Update job to cache
        $this->putCache($job);

        // Remove from nextAvailable cache if needed
        if(Cache::tags('jobs')->has('nextAvailable') && $job->status != 'available')
        {
            $nextJob = Cache::tags('jobs')->get('nextAvailable');
            if($nextJob->id == $job->id)
            {
                Cache::tags('jobs')->forget('nextAvailable');
            }
        }
    }

    /**
     * Handle the job "deleted" event.
     *
     * @param  \App\Job  $job
     * @return void
     */
    public function deleted(Job $job)
    {
        //
    }

    /**
     * Handle the job "restored" event.
     *
     * @param  \App\Job  $job
     * @return void
     */
    public function restored(Job $job)
    {
        //
    }

    /**
     * Handle the job "force deleted" event.
     *
     * @param  \App\Job  $job
     * @return void
     */
    public function forceDeleted(Job $job)
    {
        //
    }

    // Add submitter id when creating Job
    public function creating(Job $job)
    {
        $job->submitter_id = Auth::guard('api')->id();
    }

    private function putCache(Job $job)
    {
        // Save to cache
        $expiration = Config::get('cache.expiration_time');
        Cache::tags('jobs')->put('jobs.'.$job->id, $job, $expiration);
    }
}
