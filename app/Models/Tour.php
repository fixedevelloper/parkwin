<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
    protected $fillable = ['tontine_id','cycle_number','beneficiary_id','amount','status'];

    public function tontine() {
        return $this->belongsTo(Tontine::class);
    }

    public function beneficiary()
    {
        return $this->belongsTo(User::class,'beneficiary_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
