<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = ["*"];

    // Next available Job with highest priority
    public function scopeNextAvailable($query)
    {
        return $query->where('status', 'available')
                     ->orderBy('id', 'ASC')
                     ->limit(1)
                     ->get();
    }

    // Job execution
    // Sleep between 5 and 20 secs
    public function execute()
    {
        $start_time = time();
        $this->processing();
        sleep(rand(5,20));
        $this->finalize($start_time);
    }

    // Set job as processing
    private function processing()
    {
        $this->status = 'processing';
        // Set job's processor id
        $this->processor_id = getmypid();
        $this->save();
    }

    // Set job as finalized
    private function finalize($start_time)
    {
        $this->status = 'processed';
        $this->processor_id = null;
        $this->processing_time = time() - $start_time;
        $this->save();
    }
}
