<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'tour_id',
        'saving_id',
        'user_id',
        'amount',
        'method_pay',
        'reference',
        'type',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(Tour::class);
    }
}
