<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tontine extends Model
{
    protected $fillable = ['name','amount','frequency','participants_count','owner_id','reference'];

    public function members()
    {
        return $this->hasMany(TontineMember::class);
    }


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function tours() {
        return $this->hasMany(Tour::class);
    }
}
