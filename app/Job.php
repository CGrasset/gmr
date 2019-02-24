<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    // Next available Job with highest priority
    public function scopeNextAvailable($query)
    {
        return $query->where('status', 'available')
                     ->orderBy('id ASC')
                     ->first();
    }
}
