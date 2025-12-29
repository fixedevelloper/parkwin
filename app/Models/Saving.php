<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'balance',
        'amount_goal',
        'active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

