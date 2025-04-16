<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorrectionRequest extends Model
{
    protected $fillable = [
        'user_id',
        'attendance_id',
        'new_start_time',
        'new_end_time',
        'note',
        'status',
        'new_breaks',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    const STATUS_PENDING = '承認待ち';
    const STATUS_APPROVED = '承認済み';

    public static function statusOptions()
    {
        return [
            self::STATUS_PENDING => self::STATUS_PENDING,
            self::STATUS_APPROVED => self::STATUS_APPROVED,
        ];
    }
}
