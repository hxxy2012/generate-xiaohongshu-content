<?php
declare(strict_types=1);

namespace app\index\model;

use think\Model;

/**
 * 积分日志模型
 */
class PointsLog extends Model
{
    // 设置数据表
    protected $name = 'points_log';

    // 自动时间戳（仅创建）
    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = false;

    // 类型转换
    protected $type = [
        'id'         => 'integer',
        'user_id'    => 'integer',
        'points'     => 'integer',
        'related_id' => 'integer',
    ];

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
