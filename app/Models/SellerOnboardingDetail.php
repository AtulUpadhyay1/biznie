<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\Admin;

class SellerOnboardingDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'years_in_business' => 'integer',
        'current_step' => 'integer',
        'timeline' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function getUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getReviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }

    public function appendTimeline(string $event, array $meta = []): void
    {
        $timeline = $this->timeline ?? [];
        $timeline[] = array_merge([
            'event' => $event,
            'label' => match ($event) {
                'step_saved' => 'Step saved',
                'submitted' => 'Request submitted',
                'approved' => 'Request approved',
                'rejected' => 'Request rejected',
                'resubmitted' => 'Request resubmitted',
                default => $event,
            },
            'at' => now()->toIso8601String(),
        ], $meta);

        $this->timeline = $timeline;
    }
}
