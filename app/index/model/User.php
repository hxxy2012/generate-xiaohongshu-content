<?php
declare(strict_types=1);

namespace app\index\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 用户模型
 */
class User extends Model
{
    use SoftDelete;

    // 设置数据表
    protected $name = 'user';

    // 设置字段信息
    protected $schema = [
        'id'                  => 'int',
        'username'            => 'string',
        'phone'               => 'string',
        'email'               => 'string',
        'password'            => 'string',
        'nickname'            => 'string',
        'avatar'              => 'string',
        'role_id'             => 'int',
        'status'              => 'int',
        'generate_limit_daily'=> 'int',
        'generate_used_today' => 'int',
        'generate_total'      => 'int',
        'points'              => 'int',
        'vip_expire_time'     => 'datetime',
        'storage_limit'       => 'int',
        'storage_used'        => 'int',
        'inviter_id'          => 'int',
        'invite_code'         => 'string',
        'last_login_time'     => 'datetime',
        'last_login_ip'       => 'string',
        'create_time'         => 'datetime',
        'update_time'         => 'datetime',
        'delete_time'         => 'datetime',
    ];

    // 自动时间戳
    protected $autoWriteTimestamp = true;

    // 软删除字段
    protected $deleteTime = 'delete_time';

    // 隐藏字段
    protected $hidden = ['password', 'delete_time'];

    // 类型转换
    protected $type = [
        'id'                  => 'integer',
        'role_id'             => 'integer',
        'status'              => 'integer',
        'generate_limit_daily'=> 'integer',
        'generate_used_today' => 'integer',
        'generate_total'      => 'integer',
        'points'              => 'integer',
        'storage_limit'       => 'integer',
        'storage_used'        => 'integer',
        'inviter_id'          => 'integer',
    ];

    /**
     * 关联角色
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * 关联邀请人
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    /**
     * 密码加密器
     */
    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_DEFAULT);
    }

    /**
     * 生成邀请码
     */
    public function generateInviteCode(): string
    {
        return strtoupper(substr(md5(uniqid((string)mt_rand(), true)), 0, 8));
    }

    /**
     * 验证密码
     */
    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * 检查今日剩余次数
     */
    public function getRemainingQuota(): int
    {
        return max(0, $this->generate_limit_daily - $this->generate_used_today);
    }

    /**
     * 消耗生成次数
     */
    public function consumeQuota(int $count = 1): bool
    {
        if ($this->getRemainingQuota() < $count) {
            return false;
        }

        $this->generate_used_today += $count;
        $this->generate_total += $count;
        return $this->save();
    }

    /**
     * 重置今日使用次数（定时任务调用）
     */
    public static function resetDailyQuota(): void
    {
        self::where('generate_used_today', '>', 0)->update([
            'generate_used_today' => 0
        ]);
    }

    /**
     * 是否VIP
     */
    public function isVip(): bool
    {
        if (!$this->vip_expire_time) {
            return false;
        }
        return strtotime($this->vip_expire_time) > time();
    }

    /**
     * 是否企业用户
     */
    public function isEnterprise(): bool
    {
        return $this->role_id === 4;
    }

    /**
     * 检查权限
     */
    public function hasPermission(string $permissionCode): bool
    {
        $role = $this->role;
        if (!$role) {
            return false;
        }

        $permissions = $role->permissions;
        foreach ($permissions as $permission) {
            if ($permission->permission_code === $permissionCode) {
                return true;
            }
        }
        return false;
    }

    /**
     * 增加积分
     */
    public function addPoints(int $points, string $type, string $description = '', int $relatedId = 0): bool
    {
        $this->points += $points;
        $result = $this->save();

        if ($result) {
            // 记录积分日志
            PointsLog::create([
                'user_id'     => $this->id,
                'points'      => $points,
                'type'        => $type,
                'description' => $description,
                'related_id'  => $relatedId,
            ]);
        }

        return $result;
    }

    /**
     * 消耗积分
     */
    public function consumePoints(int $points, string $type, string $description = '', int $relatedId = 0): bool
    {
        if ($this->points < $points) {
            return false;
        }

        $this->points -= $points;
        $result = $this->save();

        if ($result) {
            // 记录积分日志
            PointsLog::create([
                'user_id'     => $this->id,
                'points'      => -$points,
                'type'        => $type,
                'description' => $description,
                'related_id'  => $relatedId,
            ]);
        }

        return $result;
    }
}
