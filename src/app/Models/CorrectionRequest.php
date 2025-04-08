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

    // ユーザーとのリレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 勤怠とのリレーション
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    /**
     * ステータスを定数で管理（必要に応じて）
     */
    const STATUS_PENDING = '承認待ち';
    const STATUS_APPROVED = '承認済み';

    /**
     * ステータスの選択肢を取得する（フォーム用など）
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_PENDING => '承認待ち',
            self::STATUS_APPROVED => '承認済み',
        ];
    }
}
