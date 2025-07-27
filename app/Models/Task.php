<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['workspace_id', 'title', 'description', 'deadline', 'is_completed', 'completed_at'];

    protected $appends = ['deadline_remaining', 'deadline_editable', 'completed_at_human'];

    protected $casts = [
        'deadline' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function getDeadlineRemainingAttribute(): ?string
    {
        if (!$this->deadline) {
            return null;
        }

        $end = Carbon::parse($this->deadline);
        $now = now();

        if ($end->isPast()) {
            return 'Past due';
        }

        return $now->diffForHumans($end, [
            'parts'  => 2,
            'syntax' => Carbon::DIFF_ABSOLUTE,
        ]) . ' remaining';
    }

    public function getCompletedAtHumanAttribute(): ?string
    {
        if ($this->is_completed && $this->completed_at) {
            return $this->completed_at->timezone(config('app.timezone'))->diffForHumans();
        }

        return null;
    }

    public function getDeadlineEditableAttribute(): ?string
    {
        return $this->deadline
            ? Carbon::parse($this->deadline)->format('Y-m-d\TH:i')
            : null;
    }

}
