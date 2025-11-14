<?php
declare(strict_types=1);

namespace app\index\service;

use app\index\model\ApiLog;
use think\facade\Log;

/**
 * Gemini API 服务
 */
class GeminiService
{
    private string $apiKey;
    private string $apiUrl;
    private string $model;
    private int $timeout;
    private int $maxRetries;
    private float $temperature;
    private float $topP;
    private int $topK;
    private int $maxOutputTokens;

    public function __construct()
    {
        $this->apiKey = config('gemini.api_key');
        $this->apiUrl = config('gemini.api_url');
        $this->model = config('gemini.model', 'gemini-1.5-flash');
        $this->timeout = config('gemini.timeout', 60);
        $this->maxRetries = config('gemini.max_retries', 3);
        $this->temperature = config('gemini.temperature', 0.9);
        $this->topP = config('gemini.top_p', 1);
        $this->topK = config('gemini.top_k', 40);
        $this->maxOutputTokens = config('gemini.max_output_tokens', 2048);
    }

    /**
     * 生成小红书文案
     *
     * @param array $params 生成参数
     * @return array
     */
    public function generateContent(array $params): array
    {
        // 构建Prompt
        $prompt = $this->buildPrompt($params);

        // 调用Gemini API
        $response = $this->callGeminiAPI($prompt, $params['user_id'] ?? 0);

        if (!$response['success']) {
            return [
                'success' => false,
                'error'   => $response['error'] ?? '生成失败',
            ];
        }

        // 解析返回的内容
        $content = $this->parseContentJSON($response['content']);

        if (!$content) {
            return [
                'success' => false,
                'error'   => '解析返回内容失败',
            ];
        }

        return [
            'success'    => true,
            'data'       => $content,
            'token_used' => $response['token_used'] ?? 0,
        ];
    }

    /**
     * 构建Prompt
     *
     * @param array $params
     * @return string
     */
    protected function buildPrompt(array $params): string
    {
        $keywords = $params['keywords'] ?? '';
        $contentType = $params['content_type'] ?? 'grass';
        $style = $params['style'] ?? 'casual';
        $wordCount = $params['word_count'] ?? 300;
        $targetAudience = $params['target_audience'] ?? '所有人';
        $tagCount = $params['tag_count'] ?? 5;
        $includeEmoji = $params['include_emoji'] ?? true;

        // 内容类型映射
        $typeMap = [
            'grass'    => '种草推荐（强推好物）',
            'review'   => '产品测评（详细评测）',
            'tutorial' => '知识教程（干货分享）',
            'goods'    => '好物分享（清单合集）',
            'beauty'   => '美妆护肤（美容心得）',
            'fashion'  => '穿搭分享（时尚搭配）',
            'food'     => '美食探店（吃喝推荐）',
            'travel'   => '旅行攻略（打卡指南）',
            'life'     => '生活日常（Vlog风）',
            'emotion'  => '个人感悟（情感鸡汤）',
        ];

        // 风格映射
        $styleMap = [
            'casual'       => '轻松活泼（年轻化）',
            'professional' => '专业严谨（干货风）',
            'humorous'     => '幽默诙谐（搞笑风）',
            'gentle'       => '温柔治愈（温馨风）',
            'passionate'   => '激情澎湃（励志风）',
            'cool'         => '酷炫潮流（时尚风）',
        ];

        $contentTypeText = $typeMap[$contentType] ?? $contentType;
        $styleText = $styleMap[$style] ?? $style;

        $prompt = <<<PROMPT
你是一位专业的小红书内容创作专家，擅长撰写高互动、高转化的种草文案。

请根据以下要求，创作一篇小红书风格的内容：

【基本信息】
主题/关键词：{$keywords}
内容类型：{$contentTypeText}
风格设定：{$styleText}
字数要求：{$wordCount}字左右
目标人群：{$targetAudience}

【内容要求】
1. 标题：吸睛、有悬念、带emoji，长度15-25字
2. 正文：
   - 开头要有强烈的代入感，能引起共鸣
   - 多使用短句，一句话一行
   - 适当使用emoji增强表现力（{$includeEmoji ? '必须使用' : '尽量少用'}）
   - 重点内容用【】或✨强调
   - 语气亲切，像朋友聊天
   - 多用第一人称"我"
   - 包含具体细节和数字
   - 结尾引导互动（点赞/收藏/评论）
3. 话题标签：生成{$tagCount}个相关话题标签，格式为#话题名#

【输出格式】
严格按照以下JSON格式输出（不要包含任何其他文字，不要使用markdown代码块）：
{
  "title": "标题内容",
  "content": "正文内容（使用\\n表示换行）",
  "tags": ["#标签1#", "#标签2#", "#标签3#"],
  "cover_text": "封面文字（提炼核心卖点，5-10字）"
}

【特别注意】
- 避免过度营销和硬广
- 不要出现敏感词和违规内容
- 符合小红书社区规范
- 真实、真诚、有用
- 输出必须是纯JSON格式，不要包含```json```等标记
PROMPT;

        return $prompt;
    }

    /**
     * 调用Gemini API
     *
     * @param string $prompt
     * @param int $userId
     * @return array
     */
    protected function callGeminiAPI(string $prompt, int $userId = 0): array
    {
        $startTime = microtime(true);

        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error'   => 'Gemini API Key未配置',
            ];
        }

        // 构建请求数据
        $requestData = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature'     => $this->temperature,
                'topP'           => $this->topP,
                'topK'           => $this->topK,
                'maxOutputTokens'=> $this->maxOutputTokens,
            ]
        ];

        $url = $this->apiUrl . '/models/' . $this->model . ':generateContent?key=' . $this->apiKey;

        // 重试机制
        $retryCount = 0;
        $lastError = '';

        while ($retryCount < $this->maxRetries) {
            try {
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL            => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_POSTFIELDS     => json_encode($requestData),
                    CURLOPT_HTTPHEADER     => [
                        'Content-Type: application/json',
                    ],
                    CURLOPT_TIMEOUT        => $this->timeout,
                    CURLOPT_SSL_VERIFYPEER => false,
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                $duration = (int)((microtime(true) - $startTime) * 1000);

                if ($error) {
                    throw new \Exception('CURL错误：' . $error);
                }

                $result = json_decode($response, true);

                // 记录日志
                $this->logApiCall($userId, $requestData, $result, $httpCode, $duration, true);

                if ($httpCode === 200 && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                    $content = $result['candidates'][0]['content']['parts'][0]['text'];

                    // 提取token使用情况
                    $tokenUsed = $result['usageMetadata']['totalTokenCount'] ?? 0;

                    return [
                        'success'    => true,
                        'content'    => $content,
                        'token_used' => $tokenUsed,
                    ];
                }

                $lastError = $result['error']['message'] ?? '未知错误';

            } catch (\Exception $e) {
                $lastError = $e->getMessage();

                // 记录失败日志
                $this->logApiCall($userId, $requestData, null, 0, 0, false, $lastError);
            }

            $retryCount++;
            if ($retryCount < $this->maxRetries) {
                sleep(1); // 重试前等待1秒
            }
        }

        return [
            'success' => false,
            'error'   => '请求失败：' . $lastError,
        ];
    }

    /**
     * 解析返回的JSON内容
     *
     * @param string $content
     * @return array|null
     */
    protected function parseContentJSON(string $content): ?array
    {
        // 移除可能的markdown代码块标记
        $content = preg_replace('/```json\s*|\s*```/', '', $content);
        $content = trim($content);

        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON解析失败：' . json_last_error_msg() . ' | 内容：' . $content);
            return null;
        }

        // 验证必需字段
        if (!isset($data['title']) || !isset($data['content'])) {
            Log::error('返回内容缺少必需字段');
            return null;
        }

        return [
            'title'      => $data['title'] ?? '',
            'content'    => $data['content'] ?? '',
            'tags'       => $data['tags'] ?? [],
            'cover_text' => $data['cover_text'] ?? '',
        ];
    }

    /**
     * 记录API调用日志
     */
    protected function logApiCall(
        int $userId,
        array $requestData,
        ?array $responseData,
        int $statusCode,
        int $duration,
        bool $success,
        string $errorMessage = ''
    ): void {
        try {
            ApiLog::create([
                'user_id'       => $userId ?: null,
                'api_type'      => 'gemini',
                'request_data'  => json_encode($requestData, JSON_UNESCAPED_UNICODE),
                'response_data' => $responseData ? json_encode($responseData, JSON_UNESCAPED_UNICODE) : null,
                'token_used'    => $responseData['usageMetadata']['totalTokenCount'] ?? 0,
                'status_code'   => $statusCode,
                'success'       => $success ? 1 : 0,
                'error_message' => $errorMessage,
                'duration'      => $duration,
                'ip_address'    => request()->ip(),
            ]);
        } catch (\Exception $e) {
            Log::error('记录API日志失败：' . $e->getMessage());
        }
    }
}
