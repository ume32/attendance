<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breaks()
    {
        return $this->hasMany(BreakModel::class);
    }

    public function correctionRequests()
    {
        return $this->hasMany(CorrectionRequest::class);
    }
}
