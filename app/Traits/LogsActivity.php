<?php

namespace App\Traits;

use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    /**
     * Log an activity
     */
    protected function logActivity(string $type, string $description, $subject = null, array $properties = [])
    {
        return Activity::create([
            'type' => $type,
            'description' => $description,
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id' => $subject ? $subject->id : null,
            'user_id' => Auth::id(),
            'properties' => $properties,
        ]);
    }
}

