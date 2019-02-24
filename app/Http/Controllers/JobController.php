<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class JobController extends Controller
{
    private $expiration = 120;

    // return available Job with highest priority
    public function index()
    {
    }

    public function show($id)
    {
        // Get job from cache; set to cache if not found.
        $job = Cache::tags('jobs')->remember('jobs.'.$id, $this->expiration, function () use ($id){
            return Job::findOrFail($id);
        });

        return $job;
    }

    public function store(Request $request)
    {
        // Add user_id
        $job = Job::create($request->all());

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
