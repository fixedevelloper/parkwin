<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class ScheduledSaving extends Model
{
    protected $fillable = [
        'saving_id',
        'user_id',
        'amount',
        'frequency',
        'start_date',
        'next_run_date',
        'active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'next_run_date' => 'date',
        'active' => 'boolean'
    ];

    public function saving()
    {
        return $this->belongsTo(Saving::class);
    }
}

