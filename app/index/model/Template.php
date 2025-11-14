<?php
declare(strict_types=1);

namespace app\index\model;

use think\Model;

/**
 * 图片模板模型
 */
class Template extends Model
{
    // 设置数据表
    protected $name = 'template';

    // 自动时间戳
    protected $autoWriteTimestamp = true;

    // JSON字段
    protected $json = ['config'];

    // 类型转换
    protected $type = [
        'id'               => 'integer',
        'width'            => 'integer',
        'height'           => 'integer',
        'permission_level' => 'integer',
        'use_count'        => 'integer',
        'sort'             => 'integer',
        'status'           => 'integer',
        'creator_id'       => 'integer',
        'config'           => 'array',
    ];

    // 权限等级常量
    const PERMISSION_FREE = 1;       // 免费用户
    const PERMISSION_VIP = 2;        // VIP用户
    const PERMISSION_ENTERPRISE = 3; // 企业用户

    // 分类常量
    const CATEGORY_BASIC = 'basic';      // 基础模板
    const CATEGORY_PREMIUM = 'premium';  // 高级模板
    const CATEGORY_CUSTOM = 'custom';    // 自定义模板

    /**
     * 关联创建者
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    /**
     * 增加使用次数
     */
    public function incrementUseCount(): bool
    {
        $this->use_count++;
        return $this->save();
    }

    /**
     * 检查用户是否有权限使用此模板
     */
    public function checkPermission(User $user): bool
    {
        // 自定义模板检查
        if ($this->category === self::CATEGORY_CUSTOM) {
            return $this->creator_id === $user->id || $user->isEnterprise();
        }

        // 根据权限等级检查
        if ($this->permission_level === self::PERMISSION_FREE) {
            return true;
        }

        if ($this->permission_level === self::PERMISSION_VIP) {
            return $user->isVip() || $user->isEnterprise() || $user->role_id === 1;
        }

        if ($this->permission_level === self::PERMISSION_ENTERPRISE) {
            return $user->isEnterprise() || $user->role_id === 1;
        }

        return false;
    }

    /**
     * 权限等级文本
     */
    public function getPermissionTextAttr($value, $data)
    {
        $levels = [
            self::PERMISSION_FREE       => '免费',
            self::PERMISSION_VIP        => 'VIP',
            self::PERMISSION_ENTERPRISE => '企业',
        ];
        return $levels[$data['permission_level']] ?? '未知';
    }
}
