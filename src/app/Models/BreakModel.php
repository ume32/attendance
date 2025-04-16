<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakModel extends Model
{
    protected $table = 'breaks';

    protected $fillable = [
        'attendance_id',
        'break_start',
        'break_end',
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
