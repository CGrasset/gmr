<?php

namespace App\Observers;

use App\Job;

class JobObserver
{
    private $expiration = Config::get('cache.expiration_time');
    /**
     * Handle the job "created" event.
     *
     * @param  \App\Job  $job
     * @return void
     */
    public function created(Job $job)
    {
        // Save to cache after creating
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

    private function putCache(Job $job)
    {
        Cache::tags('jobs')->put('jobs.'.$job->id, $job, $this->expiration);
    }
}
