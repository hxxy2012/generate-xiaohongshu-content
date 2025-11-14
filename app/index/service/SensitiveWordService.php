<?php
declare(strict_types=1);

namespace app\index\service;

/**
 * 敏感词过滤服务
 */
class SensitiveWordService
{
    // 敏感词列表（实际应从数据库或配置文件加载）
    private array $sensitiveWords = [
        // 政治敏感
        // '敏感词1', '敏感词2',

        // 色情低俗
        // '低俗词1', '低俗词2',

        // 违法违规
        // '违规词1', '违规词2',
    ];

    // 替换字符
    private string $replaceChar = '*';

    /**
     * 过滤内容
     *
     * @param array $data
     * @return array
     */
    public function filterContent(array $data): array
    {
        if (!empty($data['title'])) {
            $data['title'] = $this->filter($data['title']);
        }

        if (!empty($data['content'])) {
            $data['content'] = $this->filter($data['content']);
        }

        if (!empty($data['cover_text'])) {
            $data['cover_text'] = $this->filter($data['cover_text']);
        }

        return $data;
    }

    /**
     * 过滤文本
     *
     * @param string $text
     * @return string
     */
    public function filter(string $text): string
    {
        if (empty($this->sensitiveWords)) {
            return $text;
        }

        foreach ($this->sensitiveWords as $word) {
            if (stripos($text, $word) !== false) {
                $replacement = str_repeat($this->replaceChar, mb_strlen($word));
                $text = str_ireplace($word, $replacement, $text);
            }
        }

        return $text;
    }

    /**
     * 检测是否包含敏感词
     *
     * @param string $text
     * @return bool
     */
    public function hasSensitiveWord(string $text): bool
    {
        foreach ($this->sensitiveWords as $word) {
            if (stripos($text, $word) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * 获取文本中的敏感词列表
     *
     * @param string $text
     * @return array
     */
    public function getSensitiveWords(string $text): array
    {
        $found = [];
        foreach ($this->sensitiveWords as $word) {
            if (stripos($text, $word) !== false) {
                $found[] = $word;
            }
        }
        return $found;
    }
}
