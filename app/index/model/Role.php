<?php
declare(strict_types=1);

namespace app\index\model;

use think\Model;

/**
 * 角色模型
 */
class Role extends Model
{
    // 设置数据表
    protected $name = 'role';

    // 自动时间戳（仅创建）
    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = false;

    // 类型转换
    protected $type = [
        'id'     => 'integer',
        'sort'   => 'integer',
        'status' => 'integer',
    ];

    /**
     * 关联权限（多对多）
     */
    public function permissions()
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permission',
            'permission_id',
            'role_id'
        );
    }

    /**
     * 获取角色的所有权限编码
     */
    public function getPermissionCodes(): array
    {
        $permissions = $this->permissions;
        return $permissions ? $permissions->column('permission_code') : [];
    }

    /**
     * 检查角色是否有指定权限
     */
    public function hasPermission(string $permissionCode): bool
    {
        $codes = $this->getPermissionCodes();
        return in_array($permissionCode, $codes);
    }
}
