<?php

namespace App\Http\Controllers;

use App\Job;
use App\Jobs\Processor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class JobController extends Controller
{
    // return available Job with highest priority
    public function index()
    {
        // Get job from cache; set to cache if not found.
        $job = Cache::tags('jobs')->rememberForever('jobs.nextAvailable', function (){
            return Job::nextAvailable()->get();
        });

        // Forget key if nothing available to serialize
        if(empty($job->toArray())){
            Cache::tags('jobs')->forget('jobs.nextAvailable');
            return response()->json(null, 404);
        }

        return $job;
    }

    public function show($id)
    {
        $expiration = Config::get('cache.expiration_time');
        // Get job from cache; set to cache if not found.
        $job = Cache::tags('jobs')->remember('jobs.'.$id, $expiration, function () use ($id){
            return Job::findOrFail($id);
        });

        return $job;
    }

    public function store(Request $request)
    {
        // Create new job and save to cache
        $job = Job::create();

        // dispatch job!
        Processor::dispatch($job);

        return response()->json($job, 201);
    }

    public function delete(Request $request, $id)
    {
        $job = Job::findOrFail($id);
        $deleted = $job->delete();

        if($deleted)
        {
            return response()->json(null, 204);
        }else
        {
            return response()->json(["error" => 'Resource being processed. Unable to delete.'], 409);
        }

    }

    // Queue overall status
    public function queue()
    {
        $size = Cache::tags('queue')->rememberForever('queue.size', function (){
            return Job::available()->count();
        });
        $processed = Cache::tags('queue')->rememberForever('queue.processed', function (){
            return Job::processed()->count();
        });
        $avg_time = Cache::tags('queue')->rememberForever('queue.avg_time', function (){
            return Job::processed()->avg('processing_time');
        });

        $queue = [
            "size" => $size,
            "processed" => $processed,
            "avg_processing_time" => $avg_time
        ];

        return response()->json($queue);
    }
}
