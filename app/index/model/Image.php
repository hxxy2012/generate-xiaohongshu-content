<?php
declare(strict_types=1);

namespace app\index\model;

use think\Model;
use think\model\concern\SoftDelete;

/**
 * 图片模型
 */
class Image extends Model
{
    use SoftDelete;

    // 设置数据表
    protected $name = 'image';

    // 软删除字段
    protected $deleteTime = 'delete_time';

    // 自动时间戳（仅创建）
    protected $autoWriteTimestamp = 'datetime';
    protected $updateTime = false;

    // 类型转换
    protected $type = [
        'id'             => 'integer',
        'user_id'        => 'integer',
        'content_id'     => 'integer',
        'template_id'    => 'integer',
        'file_size'      => 'integer',
        'width'          => 'integer',
        'height'         => 'integer',
        'has_watermark'  => 'integer',
        'page_number'    => 'integer',
        'is_favorite'    => 'integer',
        'download_count' => 'integer',
    ];

    /**
     * 关联用户
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 关联内容
     */
    public function content()
    {
        return $this->belongsTo(Content::class, 'content_id');
    }

    /**
     * 关联模板
     */
    public function template()
    {
        return $this->belongsTo(Template::class, 'template_id');
    }

    /**
     * 增加下载次数
     */
    public function incrementDownloadCount(): bool
    {
        $this->download_count++;
        return $this->save();
    }

    /**
     * 获取完整URL
     */
    public function getFullUrlAttr($value, $data)
    {
        if (!empty($data['file_url'])) {
            return $data['file_url'];
        }
        return request()->domain() . '/' . ltrim($data['file_path'], '/');
    }

    /**
     * 格式化文件大小
     */
    public function getFileSizeTextAttr($value, $data)
    {
        $size = $data['file_size'] ?? 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return round($size, 2) . $units[$i];
    }
}
