<?php

namespace App\Http\Controllers;

use App\Job;
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
            return Job::nextAvailable();
        });

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

        return response()->json($job, 201);
    }

    // public function delete(Request $request, $id)
    // {
    //     $job = Job::findOrFail($id);
    //     $job->delete();
    //
    //     // delete from cache
    //     // TODO: DELETE IF NOT BEING PROCESSED!
    //
    //     return response()->json(null, 204);
    // }
}
