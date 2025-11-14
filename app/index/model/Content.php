<?php
declare(strict_types=1);

namespace app\index\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 内容模型（文案）
 */
class Content extends Model
{
    use SoftDelete;

    // 设置数据表
    protected $name = 'content';

    // 软删除字段
    protected $deleteTime = 'delete_time';

    // 自动时间戳
    protected $autoWriteTimestamp = true;

    // JSON字段
    protected $json = ['tags', 'generate_params'];

    // 类型转换
    protected $type = [
        'id'          => 'integer',
        'user_id'     => 'integer',
        'word_count'  => 'integer',
        'token_used'  => 'integer',
        'status'      => 'integer',
        'is_favorite' => 'integer',
        'tags'        => 'array',
        'generate_params' => 'array',
    ];

    // 状态常量
    const STATUS_DRAFT = 0;      // 草稿
    const STATUS_GENERATED = 1;  // 已生成
    const STATUS_FAILED = 2;     // 生成失败

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 关联生成的图片
     */
    public function images()
    {
        return $this->hasMany(Image::class, 'content_id');
    }

    /**
     * 状态文字
     */
    public function getStatusTextAttr($value, $data)
    {
        $status = [
            self::STATUS_DRAFT     => '草稿',
            self::STATUS_GENERATED => '已生成',
            self::STATUS_FAILED    => '失败',
        ];
        return $status[$data['status']] ?? '未知';
    }

    /**
     * 标签文本获取器
     */
    public function getTagsTextAttr($value, $data)
    {
        if (empty($data['tags'])) {
            return '';
        }
        $tags = is_string($data['tags']) ? json_decode($data['tags'], true) : $data['tags'];
        return implode(' ', $tags ?? []);
    }
}
