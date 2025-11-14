<?php
declare(strict_types=1);

namespace app\index\model;

use think\Model;

/**
 * 权限模型
 */
class Permission extends Model
{
    // 设置数据表
    protected $name = 'permission';

    // 自动时间戳（仅创建）
    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = false;

    // 类型转换
    protected $type = [
        'id' => 'integer',
    ];

    /**
     * 关联角色（多对多）
     */
    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            'role_permission',
            'role_id',
            'permission_id'
        );
    }
}
