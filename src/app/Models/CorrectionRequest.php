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
    ];

    // ユーザー
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 勤怠
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    // ステータス定数
    const STATUS_PENDING = '承認待ち';
    const STATUS_APPROVED = '承認済み';

    public static function statusOptions()
    {
        return [
            self::STATUS_PENDING => '承認待ち',
            self::STATUS_APPROVED => '承認済み',
        ];
    }
}
