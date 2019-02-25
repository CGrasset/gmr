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

        // Increment queue processed value
        if(Cache::tags('queue')->has('queue.size')){
            Cache::tags('queue')->increment('queue.size', 1);
        }else
        {
            Cache::tags('queue')->forever('queue.size', 1);
        }
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
        if(Cache::tags('jobs')->has('jobs.nextAvailable') && $job->status != 'available')
        {
            $nextJob = Cache::tags('jobs')->get('jobs.nextAvailable')[0];
            if($nextJob->id == $job->id)
            {
                Cache::tags('jobs')->forget('jobs.nextAvailable');
            }
        }

        // Decrement queue size value
        if($job->status == 'processing'){
            if(Cache::tags('queue')->has('queue.size')){
                Cache::tags('queue')->decrement('queue.size', 1);
            }
        }

        // Increment queue processed value
        if($job->status == 'processed'){
            if(Cache::tags('queue')->has('queue.processed')){
                $avg_time = Cache::tags('queue')->get('queue.avg_time');
                $processed = Cache::tags('queue')->get('queue.processed');
                $total_time = $avg_time * $processed;
                $new_avg = ($total_time + $job->processing_time)/($processed+1);
                Cache::tags('queue')->increment('queue.processed', 1);
                Cache::tags('queue')->forever('queue.avg_time', $new_avg);
            }else
            {
                Cache::tags('queue')->forever('queue.processed', 1);
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
        Cache::tags('jobs')->forget('jobs.'.$job->id);
        if($job->status == 'available' && Cache::tags('queue')->has('queue.size')){
            Cache::tags('queue')->decrement('queue.size', 1);
        }
    }

    /**
     * Handle the job "deleting" event.
     *
     * @param  \App\Job  $job
     */
    public function deleting(Job $job)
    {
        if($job->status == 'processing'){
            return false;
        }
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
